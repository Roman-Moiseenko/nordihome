<?php


namespace App\Console\Commands\Cron;


use App\Events\ThrowableHasAppeared;
use App\Modules\Shop\Parser\HttpPage;
use App\Modules\Shop\Parser\ParserService;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Console\Command;

class ParserCommand extends Command
{
    protected $signature = 'cron:parser';
    protected $description = 'Парсим цены товаров';

    public function handle()
    {
        //TODO Сделать Лог (можно через event(new LogData('Текст')))
        try {
            $service = new ParserService(new HttpPage());
            $products = ProductParser::get();
            /** @var ProductParser $product */
            foreach ($products as $product) {
                $price = $service->parserCost($product->product->code_search);
                $product->setCost($price);
            }
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
        }
    }
}
