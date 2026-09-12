<?php

namespace App\Modules\Cart\Application\DTOs;

use App\Modules\Cart\Domain\Entities\CartItemEntity;
use Spatie\LaravelData\Data;

class CartItemData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly float   $cost,
        public readonly float   $price,
        public readonly float   $quantity,
        public readonly bool    $check,
        public readonly bool    $isParser,
        //ProductInfo
        public readonly int     $productId,
        public readonly string  $name,
        public readonly string  $image,
        public readonly string  $url,
        //DiscountInfo
        public readonly ?int    $discountId,
        public readonly ?float  $discountPrice,
        public readonly ?string $discountName,

    )
    {
    }

    public function fromEntity(CartItemEntity $item)
    {

    }
}
