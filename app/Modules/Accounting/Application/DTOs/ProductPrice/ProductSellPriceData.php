<?php

namespace App\Modules\Accounting\Application\DTOs\ProductPrice;

readonly class ProductSellPriceData
{
    public function __construct(
        public int $productId,
        public float $basePrice,
        public float $sellPrice,
        public ?int $discountId,
        public ?string $discountType,
        public ?string $discountName,
    )
    {

    }
}
