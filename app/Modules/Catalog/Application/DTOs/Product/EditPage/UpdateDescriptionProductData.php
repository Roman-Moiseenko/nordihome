<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdateDescriptionProductData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,

        #[Nullable, StringType]
        public readonly ?string $description = null,

        #[Nullable, StringType]
        public readonly ?string $short = null,

        #[Nullable, StringType]
        public readonly ?string $care = null,

        #[Nullable, StringType]
        public readonly ?string $model = null,

        /** @var array<int, int|string> */
        #[Nullable]
        public readonly ?array $tags = null,

        /** ID существующей серии либо название новой серии (allow-create). */
        #[Nullable]
        public readonly int|string|null $seriesId = null,

        #[Nullable, BooleanType]
        public readonly ?bool $modification = null,
    ) {
    }
}
