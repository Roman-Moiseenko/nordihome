<?php

namespace App\Modules\Shop\Application\DTOs\Parts;
class CategoryInfo
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $image,
        public readonly int $depth,
        public readonly ?int $parentId = null,
        public readonly int $totalProducts = 0,
        /** @var ChildrenData[] $children */
        public readonly array $children,
    )
    {
    }
}

