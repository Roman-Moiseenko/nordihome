<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Post;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PostUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $template,
        #[Nullable, StringType, Max(500)]
        public readonly ?string $caption,
        #[Nullable, StringType]
        public readonly ?string $fragment,
        #[Nullable, Numeric]
        public readonly ?int $categoryId,
        #[Nullable, BooleanType]
        public readonly ?bool $published,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $metaTitle,
        #[Nullable, StringType, Max(500)]
        public readonly ?string $metaDescription,
    ) {}
}
