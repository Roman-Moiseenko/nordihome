<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdateBonusProductData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,

        #[Nullable, Numeric]
        public readonly ?int $productId = null,

        #[Nullable, StringType]
        public readonly ?string $action = null,

        /** @var array<int, array{id: int, discount?: int}>|null */
        #[Nullable]
        public readonly ?array $bonus = null,

        #[Nullable, BooleanType]
        public readonly ?bool $modification = null,
    ) {
    }
}
