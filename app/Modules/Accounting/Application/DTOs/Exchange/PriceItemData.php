<?php

namespace App\Modules\Accounting\Application\DTOs\Exchange;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PriceItemData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $code,
        #[Required, Numeric, Min(0)]
        public float $retail,
        #[Nullable, Min(0)]
        public ?float $bulk = null,
        #[Nullable, Min(0)]
        public ?float $special = null,
        #[Nullable, Min(0)]
        public ?float $minimal= null,
        #[Nullable, Min(0)]
        public ?float $cost= null,
    ) {}
}
