<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\PriceOutbox;

use App\Modules\Accounting\Application\DTOs\PriceOutbox\PriceOutboxCreateData;
use App\Modules\Accounting\Domain\Entities\PriceOutboxEntity;
use App\Modules\Accounting\Domain\Interfaces\PriceOutboxRepositoryInterface;

readonly class CreatePriceOutboxUseCase
{
    public function __construct(
        private PriceOutboxRepositoryInterface $priceOutboxRepository,
    ) {}

    public function execute(PriceOutboxCreateData $dto): PriceOutboxEntity
    {
        $entity = new PriceOutboxEntity(
            code: $dto->code,
            price: $dto->price,
            priceIkea: $dto->priceIkea,
        );

        return $this->priceOutboxRepository->save($entity);
    }
}
