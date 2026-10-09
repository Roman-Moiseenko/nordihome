<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use App\Modules\Catalog\Domain\Entities\ProductEntity;

class ViewAttributeProductData
{
    /**
     * @param array<int, array<string, mixed>> $attributes
     * @param array<int, array{id: int, name: string}> $possibleAttributes
     */
    public function __construct(
        public int $id,
        public array $attributes,
        public array $possibleAttributes,
        public bool $hasModification,
    )
    {
    }

    /**
     * @param array<int, array<string, mixed>> $attributes
     * @param array<int, array{id: int, name: string}> $possibleAttributes
     */
    public static function fromEntity(
        ProductEntity $entity,
        array $attributes = [],
        array $possibleAttributes = [],
    ): self {
        return new self(
            id: $entity->id,
            attributes: $attributes,
            possibleAttributes: $possibleAttributes,
            hasModification: $entity->hasModification,
        );
    }
}
