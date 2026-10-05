<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Equivalent;

use App\Modules\Catalog\Domain\Entities\EquivalentEntity;

class EquivalentIndexData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $category,
        public int $quantity,
    ) {}

    public static function fromEntity(EquivalentEntity $equivalent, string $category, int $quantity): self
    {
        return new self(
            id: $equivalent->id,
            name: $equivalent->name,
            category: $category,
            quantity: $quantity,
        );
    }
}
