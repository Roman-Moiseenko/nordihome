<?php

namespace App\Modules\Shop\Application\DTOs\Entities;
use App\Modules\Shop\Application\DTOs\Elements\ChildrenData;
use App\Modules\Shop\Application\DTOs\Elements\UrlData;

class CategoryRoomSecondData
{
    public function __construct(
        /** @var ChildrenData[] $children */
        public readonly array $children,
        public readonly UrlData $back,
        public readonly string $entity,
    )
    {
    }

}

