<?php

namespace App\Console\Commands\Cron;

use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Job\ParserProductsByCategory;
use App\Modules\Parser\Service\ParserIkea;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Tests\CreatesApplication;

/**
 * Парсим новые (!) товары из каталогов Икеа
 */
class IkeaProductCommand extends Command
{
    use CreatesApplication;
    protected $signature = 'cron:parser-product';
    protected $description = 'Парсим Товары Икеа';

    public function handle(): void
    {
        Log::debug('IkeaProductCommand: Начало парсинга');
        /** @var CategoryParser[] $categories */
        $categories = CategoryParser::where('active', true)->get();
        foreach ($categories as $category) {
            if ($category->children()->count() == 0) {
                //Получить список товаров в категории
                Log::debug('IkeaProductCommand: Парсим категорию ' . $category->name . ' ' . $category->url);
                ParserProductsByCategory::dispatch($category);
            }
        }
    }
}
