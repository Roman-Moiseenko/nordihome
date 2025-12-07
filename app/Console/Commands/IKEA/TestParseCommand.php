<?php

namespace App\Console\Commands\IKEA;

use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Job\ParserProductsByCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Tests\CreatesApplication;

class TestParseCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'test:parser-product';
    protected $description = 'Парсим Товары Икеа';

    public function handle(): void
    {
        Log::debug('IkeaProductCommand: Начало парсинга');
        /** @var CategoryParser[] $categories */
        $category = CategoryParser::where('url', '20611')->first();

        //Получить список товаров в категории
        Log::debug('IkeaProductCommand: Парсим категорию ' . $category->name . ' ' . $category->url);
        ParserProductsByCategory::dispatch($category);

    }
}
