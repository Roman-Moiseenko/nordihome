<?php

namespace App\Console\Commands\Cron;

use App\Console\CreatesApplication;
use Illuminate\Console\Command;

class CacheCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'cron:cache';
    protected $description = 'Кешируем данные и страницы';

    public function handle(): void
    {
        //MAINDO Пересобрать кэш

    }

}
