<?php

namespace App\Modules\Guide\Application\DTOs\Addition;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Symfony\Contracts\Service\Attribute\Required;

class AdditionUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType]
        public ?string $name = null,
        #[Nullable, Numeric]
        public ?int $base = null,
        #[Nullable, StringType]
        public ?string $type = null,
        #[Nullable, StringType]
        public ?string $class = null,
        public ?string $slug = null,
        #[Nullable, BooleanType]
        public ?bool $manual = null,
        #[Nullable, BooleanType]
        public ?bool $isQuantity = null,
    ) {
    }
}
