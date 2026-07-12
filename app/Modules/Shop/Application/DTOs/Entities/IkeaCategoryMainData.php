<?php

namespace App\Modules\Shop\Application\DTOs\Entities;
use App\Modules\Shop\Application\DTOs\Elements\ChildrenData;
use App\Modules\Shop\Application\DTOs\Elements\UrlData;

class IkeaCategoryMainData
{
    public function __construct(
        public readonly int           $id,
        public readonly string        $name,
        public readonly string        $slug,
        public int                    $totalProducts = 0,
    )
    {
    }

}

