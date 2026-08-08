<?php

namespace App\Modules\Cabinet\Application\DTOs;

class OrderInfoItemData
{
    public function __construct(
        public int $productId,
        public string $name,
        public string $image,
        public int $quantity,
        public float $priceProduct, //?
        public float $priceSell,
    )
        {

        }
}
