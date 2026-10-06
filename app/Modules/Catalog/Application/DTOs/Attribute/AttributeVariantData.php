<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use App\Modules\Catalog\Domain\Entities\AttributeVariantEntity;
use Spatie\LaravelData\Data;

class AttributeVariantData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    )
    {
    }

    public static function fromEntity(AttributeVariantEntity $variant): self
    {
        return new self(
            id: $variant->id,
            name: $variant->name,
        );
    }
}
