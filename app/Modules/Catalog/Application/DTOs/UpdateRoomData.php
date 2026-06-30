<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdateRoomData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug,
        #[Nullable, Numeric]
        public readonly ?int $parentId,
        #[Nullable, StringType]
        public readonly ?string $svgIcon,
        #[Nullable, BooleanType]
        public readonly ?bool $published,
        // Meta
        #[Nullable, StringType, Max(255)]
        public readonly ?string $metaTitle,
        #[Nullable, StringType, Max(500)]
        public readonly ?string $metaDescription,
    )
    {
    }
}
