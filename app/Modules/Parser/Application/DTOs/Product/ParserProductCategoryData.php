<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\DTOs\Product;

use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use Spatie\LaravelData\Data;

/**
 * DTO для списка товаров категории (на странице Show).
 * Содержит только то, что нужно для отображения в таблице товаров категории.
 */
class ParserProductCategoryData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $code,
        public readonly string  $name,
        public readonly string  $slug,
        public readonly ?string $image,
        public readonly bool    $available,
        public readonly bool    $fragile,
        public readonly bool    $sanctioned,
        public readonly ?int     $productId,
        public readonly float $priceSell,
        public readonly float $priceBase,
    )
    {
    }

    public static function fromEntity(ParserProductEntity $product): self
    {
        return new self(
            id: $product->id ?? 0,
            code: $product->code,
            name: $product->name,
            slug: $product->slug->getValue(),
            image: null,
            available: $product->availability,
            fragile: $product->fragile,
            sanctioned: $product->sanctioned,
            productId: $product->productId,
            priceSell: $product->priceSell,
            priceBase: $product->priceBase,
        );
    }
}
