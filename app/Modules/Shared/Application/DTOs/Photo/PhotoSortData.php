<?php

namespace App\Modules\Shared\Application\DTOs\Photo;

use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PhotoSortData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $sort,
    )
    {
    }
}
