<?php

namespace App\Modules\Order\Application\DTOs;

use App\Modules\Order\Domain\Entities\OrderHistoryStatusEntity;

readonly class OrderStatusViewData
{

    public function __construct(
        public string $value,
        public ?string $comment,
        public ?string $numberDocument,
        public ?string $dateDocument,
    )
    {

    }

    public static function fromEntity(OrderHistoryStatusEntity $entity): self
    {

        return new self(
            value: $entity->value->getValue(),
            comment: $entity->comment,
            numberDocument: $entity->numberDocument,
            dateDocument: $entity->dateDocument,
        );
    }
}
