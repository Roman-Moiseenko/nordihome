<?php

namespace App\Modules\Cabinet\Application\DTOs\Order;

class OrderInfoAdditionData
{
    public function __construct(
        public string $name,
        public int $quantity,
        public float $amount,
    )
    {
    }
}
