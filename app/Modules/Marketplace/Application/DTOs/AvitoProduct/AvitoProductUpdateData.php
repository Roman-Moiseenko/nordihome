<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

/**
 * Обновление основных полей товара Авито (блок Info).
 */
class AvitoProductUpdateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Nullable, IntegerType]
        public readonly ?int $basePrice,
        #[Nullable, IntegerType]
        public readonly ?int $sellPrice,
        #[Nullable, IntegerType]
        public readonly ?int $pricePackage,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $tnVed,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $okpd2,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $country,
        #[BooleanType]
        public readonly bool $delivery = false,
        #[BooleanType]
        public readonly bool $inStock = false,
        #[BooleanType]
        public readonly bool $active = false,
        #[BooleanType]
        public readonly bool $sale = false,
    ) {}
}
