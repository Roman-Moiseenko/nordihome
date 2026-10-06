<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\AttributeGroup;

use App\Modules\Catalog\Domain\Entities\AttributeGroupEntity;

readonly class AttributeGroupIndexData
{
    public function __construct(
        public int    $id,
        public string $name,
        public int    $quantity,
    )
    {
    }

    public static function fromEntity(AttributeGroupEntity $group, int $quantity): self
    {
        return new self(
            id: $group->id,
            name: $group->name,
            quantity: $quantity,
        );
    }
}
