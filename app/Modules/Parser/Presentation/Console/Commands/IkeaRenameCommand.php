<?php

namespace App\Modules\Parser\Presentation\Console\Commands;

use App\Console\CreatesApplication;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Infrastructure\Jobs\RenameIkeaJob;
use App\Modules\Parser\Infrastructure\Jobs\UpdateProductIkeaJob;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Parser\Job\ParserAvailablePriceProduct;
use Illuminate\Console\Command;

/**
 * Проверяем на Икеа доступность товара и новую цену
 */
class IkeaRenameCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'ikea:products-rename';
    protected $description = 'Переименовать название в оригинал';

    public function handle(ParserProductRepositoryInterface $repository): void
    {
        $products = $repository->getAll();

        $this->info('Отправка задач на переименование: ' . count($products));

        $this->output->progressStart(count($products));

        foreach ($products as $product) {
            RenameIkeaJob::dispatch($product); //Цена
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

}
