<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;

class AvitoProductCreateData extends Data
{
    public function __construct(
        #[Nullable, Numeric]
        public readonly ?int $productId = null,
    ) {}
}
