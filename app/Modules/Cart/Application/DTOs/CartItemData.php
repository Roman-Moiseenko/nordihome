<?php

namespace App\Modules\Cart\Application\DTOs;

use App\Modules\Cart\Domain\Entities\CartItem;
use Spatie\LaravelData\Data;

class CartItemData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $image,
        public readonly string  $url,
        public readonly bool    $isParser,
        public readonly int     $productId,
        public readonly float   $cost,
        public readonly float   $price,
        public readonly float   $quantity,
        public readonly ?int    $discountId,
        public readonly ?float  $discountPrice,
        public readonly ?string $discountName,
        public readonly bool    $check,
    )
    {
    }

    public function fromEntity(CartItem $item)
    {

    }
}
