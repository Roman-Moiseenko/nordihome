<?php

namespace App\Modules\Discount\Application\DTOs\PromotionProduct;

use Spatie\LaravelData\Attributes\Validation\NotIn;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PromotionProductPriceData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $productId,
        #[Required, Numeric]
        public readonly int $price,
    ){}
}
