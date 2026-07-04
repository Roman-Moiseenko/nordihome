<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ProductUpdateData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,

        // ======================== Основные поля ========================
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $namePrint = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $code = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $oldSlug = null,

        // ======================== Текстовые поля ========================
        #[Nullable, StringType]
        public readonly ?string $description = null,

        #[Nullable, StringType]
        public readonly ?string $short = null,

        #[Nullable, StringType]
        public readonly ?string $comment = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $model = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $barcode = null,

        // ======================== Числовые поля ========================
        #[Nullable, Numeric]
        public readonly ?int $mainCategoryId = null,

        #[Nullable, Numeric]
        public readonly ?int $brandId = null,

        #[Nullable, Numeric]
        public readonly ?int $seriesId = null,

        #[Nullable, Numeric]
        public readonly ?int $frequency = null,

        // ======================== Справочники (Guide) ========================
        #[Nullable, Numeric]
        public readonly ?int $vatId = null,

        #[Nullable, Numeric]
        public readonly ?int $countryId = null,

        #[Nullable, Numeric]
        public readonly ?int $measuringId = null,

        #[Nullable, Numeric]
        public readonly ?int $markingTypeId = null,

        // ======================== Булевы флаги ========================
        #[Nullable]
        public readonly ?bool $published = null,

        #[Nullable]
        public readonly ?bool $preOrder = null,

        #[Nullable]
        public readonly ?bool $delivery = null,

        #[Nullable]
        public readonly ?bool $local = null,

        #[Nullable]
        public readonly ?bool $priority = null,

        #[Nullable]
        public readonly ?bool $notSale = null,

        #[Nullable]
        public readonly ?bool $priceReduced = null,

        #[Nullable]
        public readonly ?bool $onlyOnOrder = null,

        #[Nullable]
        public readonly ?bool $fractional = null,

        #[Nullable]
        public readonly ?bool $hidePrice = null,
    )
    {
    }
}
