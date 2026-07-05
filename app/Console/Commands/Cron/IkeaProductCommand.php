<?php

namespace App\Console\Commands\Cron;

use App\Console\CreatesApplication;
use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Job\ParserProductsByCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Парсим новые (!) товары из каталогов Икеа
 */
class IkeaProductCommand extends Command
{
    use CreatesApplication;
    protected $signature = 'cron:parser-product';
    protected $description = 'Парсим Товары Икеа';

    public function handle(LoadParserProductIkeaService $service): void
    {
        $service->load();
        return;


        Log::debug('IkeaProductCommand: Начало парсинга');
        /** @var ParserCategory[] $categories */
        $categories = ParserCategory::where('active', true)->get();
        foreach ($categories as $category) {
            if ($category->children()->count() == 0) {
                //Получить список товаров в категории
                Log::debug('IkeaProductCommand: Парсим категорию ' . $category->name . ' ' . $category->ikea_id);
                ParserProductsByCategory::dispatch($category);
            }
        }
    }
}
