<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\ContentBlock;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ContentBlockUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $caption = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $section = null,
    ) {}
}
