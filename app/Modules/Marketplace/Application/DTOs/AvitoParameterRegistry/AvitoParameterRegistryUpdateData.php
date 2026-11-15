<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoParameterRegistry;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class AvitoParameterRegistryUpdateData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        #[Nullable, StringType, In(['product', 'common', 'skip', 'category'])]
        public readonly ?string $kind,
        #[Nullable, StringType]
        public readonly ?string $field,
    ) {}
}
