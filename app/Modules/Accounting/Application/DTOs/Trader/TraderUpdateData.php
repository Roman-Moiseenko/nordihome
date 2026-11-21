<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Trader;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class TraderUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name = null,
        #[Nullable, StringType]
        public readonly ?string $description = null,
        #[Nullable, BooleanType]
        public readonly ?bool $default = null,
        #[Nullable, Numeric]
        public readonly ?int $organization = null,
    ) {
    }
}
