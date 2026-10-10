<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Page;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PageCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug = null,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $template = null,
        #[Nullable, Numeric]
        public readonly ?int $parentId = null,
    ) {}
}
