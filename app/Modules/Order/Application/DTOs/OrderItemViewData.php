<?php

namespace App\Modules\Order\Application\DTOs;

readonly class OrderItemViewData
{

    public function __construct(
        public int     $id,
        public int     $productId,
        public string  $productName,
        public string  $productCode,
        public string  $productVolume,
        public string  $productWeight,
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
