<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class UpdateManagementProductData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,

        #[Nullable, BooleanType]
        public readonly ?bool $published = null,

        #[Nullable, BooleanType]
        public readonly ?bool $notSale = null,

        #[Nullable, BooleanType]
        public readonly ?bool $priority = null,

        #[Nullable, BooleanType]
        public readonly ?bool $priceReduced = null,

        #[Nullable, BooleanType]
        public readonly ?bool $hidePrice = null,

        #[Nullable, BooleanType]
        public readonly ?bool $preOrder = null,

        #[Nullable, BooleanType]
        public readonly ?bool $onlyOnOrder = null,

        /** @var array<int, array{id: int, cell?: string}>|null */
        #[Nullable]
        public readonly ?array $storages = null,

        /** @var array{min?: int, max?: int|null, buy?: bool}|null */
        #[Nullable]
        public readonly ?array $balance = null,

        #[Nullable, BooleanType]
        public readonly ?bool $modification = null,
    ) {
    }
}
