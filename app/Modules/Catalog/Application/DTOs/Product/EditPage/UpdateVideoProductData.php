<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class UpdateVideoProductData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,

        /** @var array<int, array{url?: string, caption?: string, description?: string}>|null */
        #[Nullable]
        public readonly ?array $videos = null,

        #[Nullable, BooleanType]
        public readonly ?bool $modification = null,
    ) {
    }
}
