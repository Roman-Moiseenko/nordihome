<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product;

use App\Modules\Catalog\Domain\Entities\ProductEntity;
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
        public readonly string  $slug,
        public readonly ?string $image,
        public readonly bool    $published,   // Опубликован
        public readonly bool    $not_sale,    // Снят с продажи
    )
    {
    }

    public static function fromEntity(ProductEntity $product): self
    {
        return new self(
            id: $product->id ?? 0,
            code: (string) $product->code,
            name: $product->name,
            slug: $product->slug->getValue(),
            image: null,
            published: $product->isPublished(),
            not_sale: $product->notSale,
        );
    }
}
