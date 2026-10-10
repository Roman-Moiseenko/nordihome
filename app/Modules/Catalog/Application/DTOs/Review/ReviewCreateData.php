<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Review;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ReviewCreateData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $productId,
        #[Required, Numeric]
        public readonly int $rating,
        #[Nullable, StringType]
        public readonly ?string $text,
    ) {}
}
