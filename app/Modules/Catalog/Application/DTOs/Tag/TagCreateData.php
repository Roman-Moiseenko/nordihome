<?php

namespace App\Modules\Catalog\Application\DTOs\Tag;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class TagCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $name,
        #[Nullable, StringType, Max(255)]
        public ?string $slug,
        #[Nullable, BooleanType]
        public bool $isMain,
    )
    {}
}
