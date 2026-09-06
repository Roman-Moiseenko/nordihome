<?php

namespace App\Modules\Guide\Application\DTOs\Addition;

use App\Modules\Guide\Domain\Entities\AdditionEntity;
use Spatie\LaravelData\Data;

class AdditionViewData extends Data
{
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $slug,
        public ?int $base,
        public ?string $type,
        public ?string $class,
        public ?bool $manual,
        public ?bool $isQuantity,
    ) {
    }

    public static function fromEntity(AdditionEntity $entity): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
            slug: $entity->slug->getValue(),
            base: $entity->base,
            type: $entity->type->value,
            class: $entity->class,
            manual: $entity->manual,
            isQuantity: $entity->isQuantity,
        );
    }
}
