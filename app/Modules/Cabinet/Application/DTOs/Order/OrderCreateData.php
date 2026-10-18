<?php

namespace App\Modules\Cabinet\Application\DTOs\Order;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class OrderCreateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(16)]
        public ?string $coupon,
        #[Nullable, StringType, Max(255)]
        public ?string $commentClient,
    )
    {

    }
}
