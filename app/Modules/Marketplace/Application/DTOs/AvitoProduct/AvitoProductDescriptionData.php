<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class AvitoProductDescriptionData extends Data
{
    public function __construct(
        #[Nullable, StringType]
        public readonly ?string $description = null,
    ) {}
}
