<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Presentation\Console\Commands;

use App\Modules\Marketplace\Application\Services\Avito\AvitoProductImportLogger;
use App\Modules\Marketplace\Infrastructure\Models\AvitoProduct;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

/**
 * Очистка всех объявлений Авито (полное удаление) и файла лога загрузки.
 */
class AvitoClearProductsCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'avito:clear-products';

    protected $description = 'Удаляет все объявления Авито и файл лога загрузки';

    public function __construct(
        private readonly AvitoProductImportLogger $logger,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        $deleted = AvitoProduct::query()->delete();
        $this->info("Удалено объявлений Авито: {$deleted}");

        $this->logger->clear();
        $this->info('Файл лога загрузки удалён.');

        return self::SUCCESS;
    }
}
