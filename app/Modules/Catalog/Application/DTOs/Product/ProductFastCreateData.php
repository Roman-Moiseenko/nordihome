<?php

namespace App\Modules\Catalog\Application\DTOs\Product;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ProductFastCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, StringType, Max(255)]
        public readonly string $code,
        #[Required, Numeric]
        public readonly int $brandId,
        #[Required, Numeric]
        public readonly int $categoryId,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug,
    )
    {
    }
}
