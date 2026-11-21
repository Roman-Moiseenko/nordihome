<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Trader;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class TraderCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, StringType, Min(10), Max(12)]
        public readonly string $inn,
        #[Nullable, StringType]
        public readonly ?string $bik = null,
        #[Nullable, StringType]
        public readonly ?string $account = null,
    ) {
    }
}
