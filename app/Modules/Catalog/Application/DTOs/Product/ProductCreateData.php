<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

/**
 * DTO создания товара (Catalog/Product/Create).
 */
class ProductCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255), Unique('products', 'name')]
        public readonly string $name,

        #[Required, StringType, Max(255), Unique('products', 'name_print')]
        public readonly string $namePrint,

        #[Required, StringType, Max(255), Unique('products', 'code')]
        public readonly string $code,

        #[Required, Numeric]
        public readonly int $categoryId,

        #[Required, Numeric]
        public readonly int $brandId,

        #[Required, Numeric]
        public readonly int $measuringId,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $comment = null,

        #[Nullable, Numeric]
        public readonly ?int $countryId = null,

        #[Nullable, Numeric]
        public readonly ?int $markingTypeId = null,

        #[Nullable, Numeric]
        public readonly ?int $distributorId = null,

        #[Nullable, BooleanType]
        public readonly ?bool $fractional = null,
    ) {}

    public static function messages(): array
    {
        return [
            'name.required' => 'Введите название товара',
            'name.unique' => 'Название уже существует',
            'namePrint.required' => 'Введите название товара',
            'namePrint.unique' => 'Название уже существует',
            'code.required' => 'Введите артикул',
            'code.unique' => 'Артикул уже существует',
            'categoryId.required' => 'Выберите основную категорию',
            'brandId.required' => 'Выберите бренд',
            'measuringId.required' => 'Обязательное поле',
        ];
    }
}
