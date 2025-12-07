<?php

namespace App\Console\Commands\Cron;

use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Job\ParserPriceProduct;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

/**
 * Проверяем на Икеа доступность товара и новую цену
 */
class IkeaAvailablePriceCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'cron:parser-price';
    protected $description = 'Парсим цены и доступность товаров';

    public function handle(): void
    {
        $logger = LoggerCron::new($this->description);
        $this->info('Парсим цены и доступность товаров');

        $parser_products = ProductParser::whereHas('product', function ($query) {
            $query->where('published', true);
        })->get();

        foreach ($parser_products as $parser_product) {
            ParserPriceProduct::dispatch($logger->id, $parser_product->id);
        }
    }

}
