<?php

namespace App\Modules\Shop\Application\DTOs\Parts;
class CategoryRoomMainData
{
    public function __construct(
        public readonly int           $id,
        public readonly string        $name,
        public readonly string        $slug,
        /** @var ChildrenData[] $children */
        public readonly array         $children,
        public readonly string        $entity,
        public ?UrlData               $back = null,
        public readonly ?ChildrenData $parent = null,
        public int                    $totalProducts = 0,
    )
    {
    }

}

