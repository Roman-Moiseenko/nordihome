<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Infrastructure\Jobs;

use App\Modules\Marketplace\Application\Services\Avito\AvitoProductImportLogger;
use App\Modules\Marketplace\Application\Services\Avito\AvitoProductImportService;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\QueueName;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Обработка одного листа (категории) выгрузки Авито.
 *
 * Проверяет категорию, распознаёт параметры, формирует DTO создания
 * объявления и отправляет каждое объявление в ImportAvitoProductJob.
 */
class ImportAvitoProductSheetJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $categoryPath,
        public string $sheet,
        public array $parameters,
        public array $rows,
    ) {}

    public function handle(
        AvitoCategoryRepositoryInterface $categoryRepository,
        AvitoProductImportService $importService,
        AvitoProductImportLogger $logger,
    ): void {
        $names = array_values(array_filter(array_map('trim', explode(' - ', $this->categoryPath))));
        if ($names === []) {
            return;
        }

        $category = $categoryRepository->findByNames($names);
        if ($category === null) {
            $logger->categoryNotFound($this->categoryPath);

            return;
        }

        $dtos = $importService->recognize(
            $this->categoryPath,
            $category,
            $this->sheet,
            $this->parameters,
            $this->rows,
        );

        foreach ($dtos as $dto) {
            ImportAvitoProductJob::dispatch($dto->toArray())
                ->onQueue(QueueName::DEFAULT);
        }
    }
}
