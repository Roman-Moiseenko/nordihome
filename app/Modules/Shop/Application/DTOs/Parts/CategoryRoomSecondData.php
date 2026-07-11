<?php

namespace App\Modules\Shop\Application\DTOs\Parts;
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

