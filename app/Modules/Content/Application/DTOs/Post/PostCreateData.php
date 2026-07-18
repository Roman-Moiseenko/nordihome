<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Post;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PostCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug,
        #[Required, StringType, Max(255)]
        public readonly string $template,
        #[Nullable, Numeric]
        public readonly ?int $categoryId,
    ) {}
}
