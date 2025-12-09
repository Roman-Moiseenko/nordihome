<?php

namespace App\Console\Commands\Cron;

use App\Modules\Parser\Job\ParserCategory;
use App\Modules\Parser\Service\ParserIkea;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Tests\CreatesApplication;

/**
 * Парсим новые каталоги и подкаталоги Икеа
 */
class IkeaCategoryCommand extends Command
{
    use CreatesApplication;
    protected $signature = 'cron:parser-category';
    protected $description = 'Парсим категории Икеа';

    public function handle(ParserIkea $parser): void
    {
        $categories = $parser->parserCategoriesJob();
        foreach ($categories as $category) {
            ParserCategory::dispatch($category);
        }
    }
}
