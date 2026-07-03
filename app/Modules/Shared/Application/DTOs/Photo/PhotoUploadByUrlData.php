<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\DTOs\Photo;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PhotoUploadByUrlData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $imageableId,
        #[Required, StringType, Max(255)]
        public readonly string $modelType,
        #[Required, StringType, Max(255)]
        public readonly string $type,
        #[Required, StringType]
        public readonly string $url,
    )
    {
    }
}
