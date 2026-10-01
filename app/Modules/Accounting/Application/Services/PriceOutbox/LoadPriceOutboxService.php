<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Services\PriceOutbox;

use App\Modules\Accounting\Application\DTOs\PriceOutbox\PriceOutboxViewData;
use App\Modules\Accounting\Domain\Interfaces\PriceOutboxRepositoryInterface;

readonly class LoadPriceOutboxService
{
    public function __construct(
        private PriceOutboxRepositoryInterface $priceOutboxRepository,
    ) {}

    /**
     * Возвращает все записи, помечает их progress = true и
     * преобразует в массив DTO (code, price, priceIkea).
     *
     * @return PriceOutboxViewData[]
     */
    public function execute(): array
    {
        $this->priceOutboxRepository->markAllAsProgress();

        return array_map(
            fn($entity) => PriceOutboxViewData::fromEntity($entity),
            $this->priceOutboxRepository->getAll(),
        );
    }
}
