<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\AttributeGroup;

use App\Modules\Catalog\Domain\Entities\AttributeGroupEntity;
use Spatie\LaravelData\Data;

/**
 * DTO для страницы просмотра группы атрибутов (Catalog/Attribute/Groups/Show).
 */
class AttributeGroupViewData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly ?string $svg,
        /** @var AttributeGroupAttributeData[] */
        public readonly array   $attributes = [],
    )
    {
    }

    /**
     * @param AttributeGroupAttributeData[] $attributes
     */
    public static function fromEntity(AttributeGroupEntity $group, array $attributes = []): self
    {
        return new self(
            id: $group->id,
            name: $group->name,
            svg: $group->svg,
            attributes: $attributes,
        );
    }
}
