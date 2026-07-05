<?php

namespace App\Console\Commands\Cron;

use App\Console\CreatesApplication;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Parser\Job\ParserAvailablePriceProduct;
use Illuminate\Console\Command;

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

        $parser_products = ParserProduct::where('availability', true)->get();

        foreach ($parser_products as $parser_product) {
            ParserAvailablePriceProduct::dispatch($logger->id, $parser_product->id);
        }
    }

}
