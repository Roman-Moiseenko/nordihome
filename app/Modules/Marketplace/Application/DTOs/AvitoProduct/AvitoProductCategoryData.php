<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class AvitoProductCategoryData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $avitoCategoryId,
    ) {}
}
