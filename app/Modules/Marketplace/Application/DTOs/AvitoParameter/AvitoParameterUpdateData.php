<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoParameter;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class AvitoParameterUpdateData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        #[Nullable]
        public readonly ?string $value,
    ) {}
}
