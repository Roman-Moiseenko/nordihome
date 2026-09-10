<?php

namespace App\Console\Commands\Cron;

use App\Modules\Accounting\Entity\Currency;
use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Entity\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Console\CreatesApplication;

class CurrencyCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'cron:currency';
    protected $description = 'Курс валют по ЦБ России';

    const CBR = 'https://www.cbr.ru/scripts/XML_daily.asp';

    private array $valute = [];

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        //$f_log = LoggerCron::new('Старт ' . $this->description);
        $currencies = Currency::orderBy('name')->where('cbr_code', '<>', '')->get();
        $response = Http::get(self::CBR);
        if (!$response->ok()) {
            return;
        }
        $xml = simplexml_load_string($response->body());
        $array = json_decode(json_encode($xml),true);
        $this->valute = $array['Valute'];

        foreach ($currencies as $currency) {

            $exchange = $this->getRate($currency->cbr_code);
            //$f_log->items()->create(['object' => $currency->name, 'action' => $currency->exchange, 'value' => $exchange]);
            if ($currency->setExchange($exchange)) {
                //TODO Уведомление об изменении курса
                $this->info($currency->name . ' - Установлен новый курс');


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
