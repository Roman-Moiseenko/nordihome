<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class UpdateProductSizesData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,

        #[Nullable, Numeric]
        public readonly ?int $sizeGridId = null,

        /** @var array<int, array<string, mixed>>|null */
        #[Nullable]
        public readonly ?array $sizes = null,
    ) {
    }
}
