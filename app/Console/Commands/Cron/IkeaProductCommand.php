<?php

namespace App\Console\Commands\Cron;

use App\Console\CreatesApplication;
use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use Illuminate\Console\Command;

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
    }
}
