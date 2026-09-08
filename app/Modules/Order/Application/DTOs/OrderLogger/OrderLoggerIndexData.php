<?php

namespace App\Modules\Order\Application\DTOs\OrderLogger;

use App\Modules\Order\Domain\Entities\OrderLoggerEntity;
use Spatie\LaravelData\Data;

class OrderLoggerIndexData extends Data
{
    public function __construct(
        public int $id,
        public string $createdAt,
        public string $action,
        public ?string $object,
        public ?string $old,
        public ?string $value,
        public ?string $link,
        public ?int $staffId,
        public ?string $staffName,
    )
    {

    }

    public static function fromEntity(OrderLoggerEntity $entity, ?string $staffName): self
    {
        return new self(
            id: $entity->id,
            createdAt: $entity->createdAt->format('Y-m-d H:i:s'),
            action: $entity->action,
            object: $entity->object,
            old: $entity->old,
            value: $entity->value,
            link: $entity->link,
            staffId: $entity->staffId,
            staffName: $staffName,
        );
    }
}
