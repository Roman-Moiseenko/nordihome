<?php

namespace App\Modules\Shop\Application\DTOs\Cart;

class CartInfoData
{

    public function __construct(
        /** @var CartItemData[] $items */
        public readonly array $items,
        public readonly float $amount,
        public readonly float $discount,
        public readonly int $quantity,
        public readonly float $amountCheck,
        public readonly float $discountCheck,
        public readonly int $quantityCheck,
        public readonly float $delivery,
        public readonly float $deliveryParser,
    ) {

    }
}
