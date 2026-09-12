<?php

namespace App\Modules\Discount\Application\DTOs\PromotionProduct;

use Spatie\LaravelData\Data;

class PromotionProductCartData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price
    ) {
    }
}
