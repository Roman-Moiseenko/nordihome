<?php

namespace App\Modules\Guide\Application\DTOs\Addition;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Symfony\Contracts\Service\Attribute\Required;

class AdditionCreateData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $name,
        #[Nullable, Numeric]
        public int $base = 0,
        #[Required, StringType]
        public string $type,
        #[Nullable, StringType]
        public ?string $class = null,
        public ?string $slug,
        #[Nullable, BooleanType]
        public ?bool $manual = false,
        #[Nullable, BooleanType]
        public ?bool $isQuantity = false,
    ) {
    }
}
