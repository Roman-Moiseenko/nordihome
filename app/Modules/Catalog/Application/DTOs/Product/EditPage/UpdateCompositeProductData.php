<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdateCompositeProductData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,

        #[Nullable, Numeric]
        public readonly ?int $productId = null,

        #[Nullable, StringType]
        public readonly ?string $action = null,

        #[Nullable, Numeric]
        public readonly ?int $quantity = null,

        /** @var array<int, array{id: int, quantity?: int}>|null */
        #[Nullable]
        public readonly ?array $composite = null,

        #[Nullable, BooleanType]
        public readonly ?bool $modification = null,
    ) {
    }
}
