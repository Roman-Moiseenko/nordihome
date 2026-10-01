<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Console\Commands;

use App\Modules\Catalog\Infrastructure\Job\JobSetSeriesProduct;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use Illuminate\Console\Command;

/**
 * Устанавливает серию товарам по имени из связанного ParserProduct
 */
class SetSeriesCommand extends Command
{
    protected $signature = 'catalog:set-series';
    protected $description = 'Установить серию товарам по имени из ParserProduct';

    public function handle(): void
    {
        $rows = ParserProduct::query()
            ->whereNotNull('product_id')
            ->whereNotNull('name')
            ->where('name', '<>', '')
            ->select(['product_id', 'name'])
            ->get();

        $this->info('Товаров для установки серии: ' . $rows->count());

        $this->output->progressStart($rows->count());

        foreach ($rows as $row) {
            JobSetSeriesProduct::dispatch((int) $row->product_id, (string) $row->name);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
