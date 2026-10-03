<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\PriceOutbox;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PriceOutboxCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $code,

        #[Required, Numeric]
        public readonly int $retail,

        #[Required, Numeric]
        public readonly float $sellIkea,

        #[Required, Numeric]
        public readonly int $bulk,
    ) {}
}
