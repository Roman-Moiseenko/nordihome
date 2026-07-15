<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\WidgetInstance;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class WidgetInstanceCreateData extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public readonly int $widget_id,

        #[Required, ArrayType]
        public readonly array $params = [],

        #[Nullable, StringType, Max(255)]
        public readonly ?string $title = null,

        /** ID ContentBlock для привязки (опционально) */
        #[Nullable, IntegerType]
        public readonly ?int $content_block_id = null,
    ) {}
}
