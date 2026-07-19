<?php

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\ChildrenData;
use App\Modules\Shop\Application\DTOs\Elements\UrlData;

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
        public string                 $title = '',
        public string                 $description = '',
    )
    {
    }

}

