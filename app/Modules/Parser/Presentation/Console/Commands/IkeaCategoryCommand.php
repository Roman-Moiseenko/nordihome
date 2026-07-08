<?php

namespace App\Modules\Parser\Presentation\Console\Commands;

use App\Console\CreatesApplication;
use App\Modules\Parser\Application\Services\LoadParserCategoryIkeaService;
use Illuminate\Console\Command;

/**
 * Парсим новые каталоги и подкаталоги Икеа
 */
class IkeaCategoryCommand extends Command
{
    use CreatesApplication;
    protected $signature = 'cron:parser-category';
    protected $description = 'Парсим категории Икеа';

    public function handle(LoadParserCategoryIkeaService $service): void
    {
        $service->load();
    }
}
