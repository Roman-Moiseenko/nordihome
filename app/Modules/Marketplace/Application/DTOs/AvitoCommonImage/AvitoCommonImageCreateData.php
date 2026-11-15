<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoCommonImage;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class AvitoCommonImageCreateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name,
    ) {}
}
