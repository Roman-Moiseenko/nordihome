<?php

namespace App\Modules\Order\Application\DTOs;

class AmountOrderData
{
    public function __construct(
        public float $base,

        public float $addition,
        public float $manual,
        public float $promotions,
        public float $coupon,
        public float $discount,
        public float $total,

        public float $weight,
        public float $volume,
    )
    {

    }
}
