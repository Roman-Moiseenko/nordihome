<?php

namespace App\Modules\Parser\Presentation\Console\Commands;

use App\Console\CreatesApplication;
use App\Modules\Parser\Infrastructure\Jobs\StoreProductIkeaJob;
use App\Modules\Parser\Infrastructure\Jobs\UpdateProductIkeaJob;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Parser\Job\ParserAvailablePriceProduct;
use Illuminate\Console\Command;

/**
 * Проверяем на Икеа доступность товара и новую цену
 */
class IkeaStoreCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'ikea:products-store';
    protected $description = 'Парсим кол-во товаров на складах Икеа';

    public function handle(): void
    {
        $products = ParserProduct::where('availability', true)->get();
        foreach ($products as $product) {
            StoreProductIkeaJob::dispatch($product->id); //Кол-во на складах
        }
    }

}
