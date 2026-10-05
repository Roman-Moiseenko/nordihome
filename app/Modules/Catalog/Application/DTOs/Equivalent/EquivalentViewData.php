<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Equivalent;

use App\Modules\Catalog\Domain\Entities\EquivalentEntity;
use Spatie\LaravelData\Data;

class EquivalentViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?int $categoryId,
    )
    {
    }

    public static function fromEntity(EquivalentEntity $equivalent): self
    {
        return new self(
            id: $equivalent->id,
            name: $equivalent->name,
            categoryId: $equivalent->categoryId,
        );
    }
}
