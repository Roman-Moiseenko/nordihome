<?php

namespace App\Modules\Shared\Application\DTOs\Photo;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PhotoSaveData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $alt = null,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $title = null,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $description = null,
    )
    {
    }
}
