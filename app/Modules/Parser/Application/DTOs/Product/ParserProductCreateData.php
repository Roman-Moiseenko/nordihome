<?php

namespace App\Modules\Parser\Application\DTOs\Product;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ParserProductCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, StringType, Max(255)]
        public readonly string $code,
        #[Required, StringType, Max(255)]
        public readonly string $short,
    )
    {
    }
}
