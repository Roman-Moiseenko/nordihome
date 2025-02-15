<?php

namespace App\Console\Commands\Cron;

use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Page\Service\CacheService;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Job\ParserPriceProduct;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class CacheCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'cron:cache';
    protected $description = 'Кешируем данные и страницы';

    public function handle(CacheService $service): void
    {
       //TODO $logger = LoggerCron::new($this->description);
        $this->info('Кешируем данные');

        $service->rebuildCategories();
    }

}
