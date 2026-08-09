<?php

namespace App\Modules\Order\Application\DTOs;

readonly class OrderItemViewData
{

    public function __construct(
        public int     $id,
        public ProductItemData $product,
        public float   $baseCost,
        public float   $sellCost,
        public float   $quantity,
        public bool    $preorder,
        public bool    $isDiscount,
        public float   $percentDiscount,
        public ?string $comment,

        public bool    $assemblage,
        public bool    $packing,
    )
    {

    }
}
