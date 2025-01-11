<?php

namespace App\Modules\Accounting\Service;


use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Base\Entity\BankPayment;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Service\OrderPaymentService;
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
    private OrderPaymentService $orderPaymentService;

    public function __construct(
        PaymentDocumentService $paymentDocumentService,
        SettingRepository $settings,
        OrganizationService $organizationService,
        OrderPaymentService $orderPaymentService
        //TODO Добавить сервис платежей от клиентов
    )
    {
        $this->paymentDocumentService = $paymentDocumentService;
        $common = $settings->getCommon();

        $this->date_bank = Carbon::parse($common->date_bank);
        $this->organizationService = $organizationService;
        $this->orderPaymentService = $orderPaymentService;
    }

    public function upload(Request $request): array
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
       // dd($payments);


        $this->loadPayments($section, $payments);
        return $payments;
    }

    private function loadPayments(array $section, array $payments): void
    {
        //TODO Поиск организации по расчетному счету
        $this->payer = Organization::where('pay_account', $section['РасчСчет'])->first();
        foreach ($payments as $payment) {
            if ($this->checkDate($payment['ДатаСписано'])) {
                if ($payment['ПлательщикСчет'] == $section['РасчСчет']) {
                    $this->createPaymentDocument($payment);
                } else {
                    $this->createPaymentOrder($payment);
                }
            } else {
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

    /**
     * Загрузка платежей поставщикам
     */
    private function createPaymentDocument(array $payment): void
    {

        $recipient = Organization::where('inn', $payment['ПолучательИНН'])->first();
        $payer = Organization::where('inn', $payment['ПлательщикИНН'])->first();

        if (is_null($recipient)) { //Если нет получателя, то создаем
            $recipient = $this->organizationService->create_find($payment['ПолучательИНН'], $payment['ПолучательБИК'], $payment['ПолучательСчет']);
        }

        $paymentDocument = $this->paymentDocumentService->create(
            $recipient->id,
            $payer->id,
            $payment['Сумма']);

        //TODO Переделать как в createPaymentOrder

        $paymentDocument->bank_payment = BankPayment::createFromBankPayment($payment);

        $paymentDocument->save();
        $paymentDocument->fillDecryptions();
    }

    /**
     * Загрузка платежей клиентов
     */
    private function createPaymentOrder(mixed $payment): void
    {
        $trader = Organization::where('inn', $payment['ПолучательИНН'])->first();
        $shopper = Organization::where('inn', $payment['ПлательщикИНН'])->first();

        $orders = Order::where('trader_id', $trader->id)
            ->where('shopper_id', $shopper->id)
            ->whereHas('status', function ($query) {
                $query->whereIn('value', [OrderStatus::AWAITING, OrderStatus::PREPAID]);
            })->getModels();

        $amount = $payment['Сумма'];
        $order = null;
        foreach ($orders as $_order) {
            //Выбираем заказ, у которого остаток равен платежу
            if ($_order->getTotalAmount() - $_order->getPaymentAmount() == $amount) $order = $_order;
        }

        if (is_null($order)) { //Создаем непривязанный платеж
            $orderPayment = $this->orderPaymentService->createUnresolved($shopper->id, $trader->id, $amount, OrderPayment::METHOD_ACCOUNT);
        } else {
            $orderPayment = $this->orderPaymentService->create($order, $amount, OrderPayment::METHOD_ACCOUNT);
        }
        $orderPayment->bank_payment = BankPayment::createFromBankPayment($payment);
        $orderPayment->save();

    }

    /**
     * Обновление валюты
     */
    public function currency(Request $request): void
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
