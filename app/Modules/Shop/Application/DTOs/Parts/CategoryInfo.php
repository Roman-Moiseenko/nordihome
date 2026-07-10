<?php

namespace App\Modules\Shop\Application\DTOs\Parts;
class CategoryInfo
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $image,
        public int $totalProducts = 0,
        /** @var ChildrenData[] $children */
        public readonly array $children,
        public readonly ?ChildrenData $parent = null,
    )
    {
    }

}

