<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use App\Modules\Catalog\Domain\Entities\AttributeVariantEntity;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

/**
 * DTO карточки атрибута (Catalog/Attribute/Show).
 *
 * Ключи сериализуются в snake_case, чтобы соответствовать контракту
 * фронтенда (Info.vue). Изображения не передаются: на фронтенде
 * используется компонент PhotoDTO.
 */
class AttributeViewData extends Data
{
    /**
     * @param CategoryData[] $categories
     * @param AttributeVariantData[] $variants
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $type,
        #[MapOutputName('type_text')]
        public readonly string $typeText,
        #[MapOutputName('is_variant')]
        public readonly bool $isVariant,
        #[MapOutputName('group_id')]
        public readonly ?int $groupId,
        public readonly string $group,
        public readonly bool $multiple,
        public readonly bool $filter,
        #[MapOutputName('show_in')]
        public readonly bool $showIn,
        public readonly ?string $sameAs,
        public readonly array $categories = [],
        public readonly array $variants = [],
    )
    {
    }

    /**
     * @param CategoryData[] $categories
     */
    public static function fromEntity(AttributeEntity $attribute, string $group, array $categories): self
    {
        return new self(
            id: $attribute->id,
            name: $attribute->name,
            type: (string) $attribute->type,
            typeText: $attribute->type->label(),
            isVariant: $attribute->type->isVariant(),
            groupId: $attribute->groupId,
            group: $group,
            multiple: $attribute->multiple,
            filter: $attribute->filter,
            showIn: $attribute->showIn,
            sameAs: $attribute->sameAs,
            categories: $categories,
            variants: array_map(
                fn(AttributeVariantEntity $variant) => AttributeVariantData::fromEntity($variant),
                $attribute->variants,
            ),
        );
    }
}
