<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\ContentBlock;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ContentBlockCreateData extends Data
{
    public function __construct(
        #[Required, StringType]
        public readonly string $container_type,

        #[Required, IntegerType]
        public readonly int $container_id,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $caption = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $section = null,
    ) {}
}
