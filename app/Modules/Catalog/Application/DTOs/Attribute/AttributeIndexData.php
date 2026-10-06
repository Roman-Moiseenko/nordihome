<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use App\Modules\Catalog\Domain\Entities\AttributeEntity;

/**
 * DTO строки списка атрибутов (Catalog/Attribute/Index).
 */
readonly class AttributeIndexData
{
    /**
     * @param CategoryData[] $categories
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $group,
        public string $type,
        public string $typeText,
        public bool $filter,
        public array $categories = [],
    )
    {
    }

    /**
     * @param CategoryData[] $categories
     */
    public static function fromEntity(AttributeEntity $attribute, string $group, array $categories = []): self
    {
        return new self(
            id: $attribute->id,
            name: $attribute->name,
            group: $group,
            type: (string) $attribute->type,
            typeText: $attribute->type->label(),
            filter: $attribute->filter,
            categories: $categories,
        );
    }
}
