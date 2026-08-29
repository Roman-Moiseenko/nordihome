<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\DTOs\Promotion;

use App\Modules\Catalog\Domain\Entities\ProductEntity;
use Spatie\LaravelData\Data;

/**
 * DTO товара в акции (для списка товаров акции).
 */
class PromotionProductViewData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $code,
        public readonly string  $name,
        public readonly string  $slug,
        public readonly ?string $image,
        public readonly float   $price,
        public readonly float   $discount,
        public readonly int     $quantity,
    )
    {
    }

    public static function fromEntity(ProductEntity $product, float $price, float $discount, int $quantity): self
    {
        return new self(
            id: $product->id ?? 0,
            code: (string) $product->code,
            name: $product->name,
            slug: $product->slug->getValue(),
            image: null,
            price: $price,
            discount: $discount,
            quantity: $quantity,
        );
    }
}
