<?php

namespace App\Modules\Catalog\Application\DTOs\ProductPrice;

readonly class ProductSellPriceData
{
    public function __construct(
        public int $productId,
        public float $basePrice,
        public float $sellPrice,
        public ?int $discountId,
        public ?int $discountType,
    )
    {

    }
}
