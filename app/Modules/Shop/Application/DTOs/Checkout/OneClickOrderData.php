<?php

namespace App\Modules\Shop\Application\DTOs\Checkout;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class OneClickOrderData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public int $productId,
        #[Required, Email]
        public string $email,
        #[Required, StringType]
        public string $phone,
        #[Required, BooleanType]
        public bool $isPickup,
        #[Nullable, StringType]
        public ?string $region,
        #[Nullable, Numeric]
        public ?int $regionCode,
        #[Nullable, StringType]
        public ?string $address,
        #[Nullable, StringType]
        public ?string $name = null,
        #[Nullable, BooleanType]
        public readonly bool $agreement = false,
    )
    {

    }
}
