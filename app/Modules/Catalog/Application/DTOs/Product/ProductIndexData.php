<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product;

use App\Modules\Catalog\Domain\Entities\ProductEntity;

/**
 * DTO строки списка товаров (Catalog/Product/Index).
 *
 * $categoryName и $trashed — вычисляемые значения, которых нет в
 * ProductEntity, поэтому передаются в fromEntity отдельно.
 * Изображение (image) загружается на фронтенде отдельным запросом,
 * поэтому всегда null.
 */
readonly class ProductIndexData
{
    public function __construct(
        public int     $id,
        public string  $code,
        public string  $name,
        public string  $slug,
        public bool    $published,
        public bool    $notSale,
        public bool    $trashed,
        public string  $categoryName,
        public ?string $image = null,
    ) {}

    public static function fromEntity(ProductEntity $product, string $categoryName, bool $trashed): self
    {
        return new self(
            id: $product->id ?? 0,
            code: (string) $product->code,
            name: $product->name,
            slug: (string) $product->slug,
            published: $product->isPublished(),
            notSale: $product->notSale,
            trashed: $trashed,
            categoryName: $categoryName,
        );
    }
}
