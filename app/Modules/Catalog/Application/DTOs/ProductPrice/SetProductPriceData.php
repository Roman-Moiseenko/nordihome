<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\ProductPrice;

use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class SetProductPriceData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $productId,

        #[Required, Numeric]
        public readonly float $price,

        #[Required, StringType]
        public readonly string $priceType,

        public readonly ?string $founded = null,

        public readonly ?string $comment = null,
    )
    {
    }
}
