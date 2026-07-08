<?php

namespace App\Modules\Parser\Presentation\Console\Commands;

use App\Console\CreatesApplication;
use App\Modules\Parser\Infrastructure\Jobs\UpdateProductIkeaJob;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Parser\Job\ParserAvailablePriceProduct;
use Illuminate\Console\Command;

/**
 * Проверяем на Икеа доступность товара и новую цену
 */
class IkeaAvailablePriceCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'cron:parser-update';
    protected $description = 'Парсим цены и доступность товаров';

    public function handle(): void
    {
        $products = ParserProduct::where('availability', true)->get();
        foreach ($products as $product) {
            UpdateProductIkeaJob::dispatch($product->id); //Цена
            //TODO добавить парсинг кол-ва по складам
        }
    }

}
