<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use Spatie\LaravelData\Data;

/**
 * DTO категории, к которой привязан атрибут (для списка атрибутов).
 * Категорий у атрибута немного, поэтому простой массив без пагинации.
 */
class CategoryData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
    )
    {
    }

    public static function fromEntity(CategoryEntity $category): self
    {
        return new self(
            id: $category->id ?? 0,
            name: $category->name,
            slug: (string) $category->slug,
        );
    }
}
