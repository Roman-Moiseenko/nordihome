<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use App\Modules\Catalog\Domain\Entities\AttributeVariantEntity;
use Spatie\LaravelData\Data;

/**
 * DTO карточки атрибута (Catalog/Attribute/Show).
 */
class AttributeViewData extends Data
{
    /**
     * @param AttributeVariantData[] $variants
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $typeText,
        public readonly ?int $groupId,
        public readonly bool $multiple,
        public readonly bool $filter,
        public readonly bool $showIn,
        public readonly ?string $sameAs,
        public readonly array $variants = [],
    )
    {
    }

    public static function fromEntity(AttributeEntity $attribute): self
    {
        return new self(
            id: $attribute->id,
            name: $attribute->name,
            type: (string) $attribute->type,
            typeText: $attribute->type->label(),
            groupId: $attribute->groupId,
            multiple: $attribute->multiple,
            filter: $attribute->filter,
            showIn: $attribute->showIn,
            sameAs: $attribute->sameAs,
            variants: array_map(
                fn(AttributeVariantEntity $variant) => AttributeVariantData::fromEntity($variant),
                $attribute->variants,
            ),
        );
    }
}
