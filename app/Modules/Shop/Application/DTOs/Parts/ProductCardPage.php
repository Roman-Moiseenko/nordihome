<?php

namespace App\Modules\Shop\Application\DTOs\Parts;

class ProductCardPage
{
    public function __construct(
        /** @var array[] */
        public readonly array $products,
        /** @var PaginatorData */
        public readonly PaginatorData $paginator,
    )
    {
    }
}
