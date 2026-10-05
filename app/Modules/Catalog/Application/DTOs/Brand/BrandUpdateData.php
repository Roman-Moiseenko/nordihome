<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Brand;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class BrandUpdateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $url,
        #[Nullable, StringType]
        public readonly ?string $description,
        #[Nullable, Numeric]
        public readonly ?int $currencyId,
        #[Nullable]
        public readonly ?array $sameAs,
    )
    {
    }
}
