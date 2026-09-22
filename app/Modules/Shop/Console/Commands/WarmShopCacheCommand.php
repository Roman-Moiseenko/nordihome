<?php

declare(strict_types=1);

namespace App\Modules\Shop\Console\Commands;

use App\Modules\Shared\Domain\ValueObjects\QueueName;
use App\Modules\Shop\Infrastructure\Jobs\WarmShopCacheJob;
use Illuminate\Console\Command;

class WarmShopCacheCommand extends Command
{
    protected $signature = 'shop:cache-warm';
    protected $description = 'Прогрев кешей модуля Shop (задача ставится в очередь photo)';

    public function handle(): int
    {
        WarmShopCacheJob::dispatch()->onQueue(QueueName::PHOTO);

        $this->info('Задача прогрева кеша модуля Shop поставлена в очередь "' . QueueName::PHOTO . '".');

        return self::SUCCESS;
    }
}
