<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Parser\Domain\ValueObjects\Package;

class ViewDimensionsProductData
{
    /**
     * @param array<string, mixed> $dimensions
     * @param array<int, array<string, mixed>> $packages
     */
    public function __construct(
        public int $id,
        public array $dimensions,
        public array $packages,
        public bool $local,
        public bool $delivery,
        public ?string $complexity,
        public bool $hasModification,
    )
    {
    }

    public static function fromEntity(ProductEntity $entity): self
    {
        return new self(
            id: $entity->id,
            dimensions: $entity->dimensions?->toArray() ?? [],
            packages: array_map(
                fn(Package $package) => $package->toArray(),
                $entity->packages,
            ),
            local: $entity->local,
            delivery: $entity->delivery,
            complexity: $entity->complexity,
            hasModification: $entity->hasModification,
        );
    }
}
