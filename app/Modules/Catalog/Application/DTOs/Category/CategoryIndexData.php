<?php

namespace App\Modules\Catalog\Application\DTOs\Category;

use App\Modules\Catalog\Application\DTOs\Room\RoomIndexData;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class CategoryIndexData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly int $depth,
        public readonly bool $published,
        public readonly ?string $image_url,
        public readonly ?string $icon_url,
        /** @var CategoryIndexData[] */
        public readonly array $children = []
    )
    {
    }

    public static function fromEntity(CategoryEntity $category): self
    {
        return new self(
            id: $category->id,
            name: $category->name,
            slug: (string) $category->slug,
            depth: $category->depth,
            published: $category->isPublished(),
            image_url: $category->image_url,
            icon_url: $category->icon_url,
            children: array_map(
                fn(CategoryEntity $child) => self::fromEntity($child),
                $category->children
            ),
        );
    }
}
