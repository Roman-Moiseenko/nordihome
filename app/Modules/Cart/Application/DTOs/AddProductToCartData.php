<?php

namespace App\Modules\Cart\Application\DTOs;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class AddProductToCartData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public int $id,
        #[Nullable, Numeric]
        public readonly int $quantity = 1,
        #[Nullable, BooleanType]
        public readonly bool $isParser = false,

    )
    {

    }
}
