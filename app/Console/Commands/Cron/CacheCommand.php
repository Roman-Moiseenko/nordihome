<?php

namespace App\Console\Commands\Cron;

use App\Console\CreatesApplication;
use App\Modules\Page\Service\CacheService;
use Illuminate\Console\Command;

class CacheCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'cron:cache';
    protected $description = 'Кешируем данные и страницы';

    public function handle(CacheService $service): void
    {
        $this->info('Кешируем данные');

        $service->rebuildCache();
    }

}
