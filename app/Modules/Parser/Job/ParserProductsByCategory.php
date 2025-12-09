<?php

namespace App\Modules\Parser\Job;

use App\Modules\Parser\Entity\CategoryParser;
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

    private CategoryParser $category;

    public function __construct(CategoryParser $category)
    {
        $this->category = $category;
    }

    public function handle(ParserIkea $parserIkea): void
    {
        Log::debug('ParserProductsByCategory: Начало');

        $products = $parserIkea->getProductsByCategoryJob($this->category->url);
        Log::debug('ParserProductsByCategory: Список товаров: ');
        foreach ($products as $product) {
            Log::debug($product['itemNoGlobal']);
            $product['parser_category_id'] = $this->category->id;
            CreateParserProduct::dispatch($product);
        }
    }
}
