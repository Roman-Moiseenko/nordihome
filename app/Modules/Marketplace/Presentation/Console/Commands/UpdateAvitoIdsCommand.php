<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Presentation\Console\Commands;

use App\Modules\Marketplace\Infrastructure\Jobs\UpdateAvitoProductIdNumberJob;
use App\Modules\Marketplace\Infrastructure\Models\AvitoProduct;
use App\Modules\Shared\Domain\ValueObjects\QueueName;
use Illuminate\Console\Command;

class UpdateAvitoIdsCommand extends Command
{
    protected $signature = 'avito:update-id';

    protected $description = 'Обновляет ad_number (avito_id) для активных объявлений Авито без номера';

    public function handle(): int
    {
        $products = AvitoProduct::query()
            ->where('active', true)
            ->whereNull('ad_number')
            ->whereNotNull('ad_id')
            ->get(['id', 'ad_id']);

        foreach ($products as $product) {
            UpdateAvitoProductIdNumberJob::dispatch($product->id, (string) $product->ad_id)
                ->onQueue(QueueName::DEFAULT);
        }

        $this->info("Поставлено задач в очередь \"" . QueueName::DEFAULT . "\": {$products->count()}.");

        return self::SUCCESS;
    }
}
