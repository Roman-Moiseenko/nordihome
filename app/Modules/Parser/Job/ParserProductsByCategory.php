<?php

namespace App\Modules\Parser\Job;

use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Service\ParserIkea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job для запуска по крону команды IkeaProductCommand - парсим список товаров из категории
 */
class ParserProductsByCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ParserCategory $category;

    public function __construct(ParserCategory $category)
    {
        $this->category = $category;
    }

    public function handle(ParserIkea $parserIkea): void
    {
        Log::debug('ParserProductsByCategory: Начало');

        $products = $parserIkea->getProductsByCategoryJob($this->category->ikea_id);
        Log::debug('ParserProductsByCategory: Список товаров: ');
        foreach ($products as $product) {
            Log::debug($product['itemNoGlobal']);
            $product['parser_category_id'] = $this->category->id;
            CreateParserProduct::dispatch($product);
        }
    }
}
