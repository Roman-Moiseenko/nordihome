<?php

namespace App\Modules\Shared\Application\DTOs\Photo;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PhotoByEntityListData extends Data
{
    public function __construct(
        #[Required, ArrayType]
        public readonly array $imageableIds,
        #[Required, StringType]
        public readonly string $modelType,
        #[Required, StringType]
        public readonly string $type,
    )
    {
    }
}
