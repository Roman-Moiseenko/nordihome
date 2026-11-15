<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoCommonImage;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class AvitoCommonImageUpdateData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name,
        #[Nullable, BooleanType]
        public readonly ?bool $active,
    ) {}
}
