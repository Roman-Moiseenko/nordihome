<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Category;

use App\Modules\Catalog\Application\DTOs\Room\RoomViewData;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use Spatie\LaravelData\Data;

class CategoryViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $svgIcon,
        public readonly bool $published,
        public readonly ?array $meta,
        public readonly ?int $parentId,
        public readonly int $left,
        public readonly int $right,
        public readonly int $depth,
        /** @var CategoryViewData[] */
        public readonly array $children = [],
    )
    {
    }

    public static function fromEntity(CategoryEntity $category): self
    {
        return new self(
            id: $category->id,
            name: $category->name,
            slug: (string) $category->slug,
            svgIcon: $category->svgIcon,
            published: $category->isPublished(),
            meta: $category->meta ? [
                'title' => $category->meta->getTitle(),
                'description' => $category->meta->getDescription(),
            ] : null,
            parentId: $category->parentId,
            left: $category->left,
            right: $category->right,
            depth: $category->depth,
            children: array_map(
                fn(CategoryEntity $child) => self::fromEntity($child),
                $category->children
            ),
        );
    }
}
