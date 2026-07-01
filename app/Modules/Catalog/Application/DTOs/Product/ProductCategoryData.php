<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product;

use Spatie\LaravelData\Data;

/**
 * DTO для списка товаров категории (на странице Show).
 * Содержит только то, что нужно для отображения в таблице товаров категории.
 */
class ProductCategoryData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $code,
        public readonly string  $name,
        public readonly ?string $image,
        public readonly bool    $published,   // Опубликован
        public readonly bool    $not_sale,    // Снят с продажи
    )
    {
    }
}
