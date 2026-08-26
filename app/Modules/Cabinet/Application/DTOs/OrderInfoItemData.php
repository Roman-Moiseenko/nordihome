<?php

namespace App\Modules\Cabinet\Application\DTOs;

class OrderInfoItemData
{
    public function __construct(
        public int    $productId,
        public string $productName,
        public string $productCode,
        public string $productImage,
        public bool   $productPublished,
        public bool   $productParser,
        public string $productSlug,

        public int    $quantity,
        public float  $baseCost, //?
        public float  $sellCost,
        public bool   $preorder,
        public ?int   $discountId,
    )
    {

    }
}
