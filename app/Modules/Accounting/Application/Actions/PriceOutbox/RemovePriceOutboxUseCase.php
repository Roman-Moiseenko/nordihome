<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\PriceOutbox;

use App\Modules\Accounting\Domain\Interfaces\PriceOutboxRepositoryInterface;

readonly class RemovePriceOutboxUseCase
{
    public function __construct(
        private PriceOutboxRepositoryInterface $priceOutboxRepository,
    ) {}

    public function execute(): void
    {
        $this->priceOutboxRepository->deleteAllWithProgress();
    }
}
