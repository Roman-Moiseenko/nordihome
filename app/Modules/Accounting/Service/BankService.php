<?php

namespace App\Modules\Accounting\Service;


use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Entity\Setting;
use App\Modules\Setting\Repository\SettingRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BankService
{

    const CBR = 'https://www.cbr.ru/scripts/XML_daily.asp';

    private PaymentDocumentService $paymentDocumentService;
    private mixed $date_bank;
    private OrganizationService $organizationService;
    private Organization $payer;
    private array $valute = [];

    public function __construct(
        PaymentDocumentService $paymentDocumentService,
        SettingRepository $settings,
        OrganizationService $organizationService,
        //TODO Добавить сервис платежей от клиентов
    )
    {
        $this->paymentDocumentService = $paymentDocumentService;
        $common = $settings->getCommon();
        $this->date_bank = Carbon::parse($common->date_bank);
        $this->organizationService = $organizationService;
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');
        $payment = [];
        $section = [];
        $payments = [];
        $flag_order = false;
        $flag_amount = false;
        foreach (file($file) as $line) {
            $key = null;
            $value = null;
            $line = mb_convert_encoding($line, 'UTF-8', 'WINDOWS-1251');
            $line = str_replace(array("\r", "\n"), '', $line);
            preg_match('/^(.*)=(.*)$/', $line, $match);
            if (!empty($match)) {
                $key = $match[1];
                $value = $match[2];
            } else {
                if (trim($line) != '') $key = $line;
            }
            //Данные о счете и даты
            if ($key == 'СекцияРасчСчет') $flag_amount = true;
            if ($flag_amount) {
                $section[$key] = $value;
                if ($key == 'КонецРасчСчет') $flag_amount = false;
            }
            //Платежки
            if ($key == 'СекцияДокумент' && $value == 'Платежное поручение') $flag_order = true;
            if ($flag_order) {
                $payment[$key] = $value;
                if ($key == 'КонецДокумента') {
                    $flag_order = false;
                    $payments[] = $payment;
                    $payment = [];
                }
            }
        }
        $this->loadPayments($section, $payments);
        return $payments;
    }

    private function loadPayments(array $section, array $payments): void
    {
        $this->payer = Organization::where('pay_account', $section['РасчСчет'])->first();
        foreach ($payments as $payment) {
            if ($this->checkDate($payment['ДатаСписано'])) {
                if ($payment['ПлательщикСчет'] == $section['РасчСчет']) {
                    $this->createPaymentDocument($payment);
                } else {
                    $this->createPaymentOrder($payment);
                }
            }
        }

        ;
        //Записываем Дату
        $setting = Setting::where('slug', 'common')->first();
        $common = new Common($setting->getData());
        $common->date_bank = $section['ДатаКонца'];
        $setting->setData($common);
    }

    private function checkDate(string $dateDocument): bool
    {
        return $this->date_bank->lt(Carbon::parse($dateDocument));
    }

    private function createPaymentDocument(array $payment): void
    {

        $recipient = Organization::where('inn', $payment['ПолучательИНН'])->first();
        $payer = Organization::where('inn', $payment['ПлательщикИНН'])->first();

        if (is_null($recipient)) { //Если нет получателя, то создаем
            $recipient = $this->organizationService->create_find($payment['ПолучательИНН'], $payment['ПолучательБИК'], $payment['ПолучательСчет']);
        }

        $paymentDocument = $this->paymentDocumentService->create(
            $recipient->id, $payment['ПолучательСчет'],
            $payer->id, $payment['ПлательщикРасчСчет'],
            $payment['Сумма']);
        $paymentDocument->bank_purpose = $payment['НазначениеПлатежа'];
        $paymentDocument->bank_number = $payment['Номер'];
        $paymentDocument->bank_date = Carbon::parse($payment['Дата']);
        $paymentDocument->save();
        $paymentDocument->fillDecryptions();
    }

    private function createPaymentOrder(mixed $payment)
    {
        //TODO Загрузка платежей клиентов
    }

    /**
     * Обновление валюты
     */
    public function currency(Request $request)
    {
        $currency_id = $request->input('currency_id');
        $query = Currency::orderBy('name')->where('cbr_code', '<>', '');
        if (!is_null($currency_id)) $query->where('id', $currency_id);
        $currencies = $query->getModels();
        if (empty($currencies)) throw new \DomainException('Нет валют для обновления');

        $response = Http::get(self::CBR);

        if (!$response->ok()) throw new \DomainException('Нет ответа ЦБ');
        $xml = simplexml_load_string($response->body());
        $array = json_decode(json_encode($xml),true);
        $this->valute = $array['Valute'];

        foreach ($currencies as $currency) {
            $exchange = $this->getRate($currency->cbr_code);
            if ($currency->setExchange($exchange)) {
                $logger = LoggerCron::new('Курс валют по ЦБ России');
                $logger->items()->create([
                    'object' => $currency->name,
                    'action' => 'Новый курс',
                    'value' => $exchange,
                ]);
                //Если злоты, меняем в Настройках Парсера
                if ($currency->cbr_code == 'PLN') {
                    $setting = Setting::where('slug', 'parser')->first();
                    $parser = new Parser($setting->getData());
                    $parser->parser_coefficient = $currency->getExchange();
                    $setting->setData($parser);
                }
            }
        }

    }

    private function getRate(string $cbr_code): float
    {
        foreach ($this->valute as $item) {
            if ($item['CharCode'] == $cbr_code) return (float)str_replace(',', '.', $item['VunitRate']);
        }
        return 0;
    }
}
