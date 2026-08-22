<?php

namespace App\Modules\Order\Application\DTOs\OrderItem;

use Spatie\LaravelData\Data;

class OrderItemPreData extends Data
{
    public function __construct(
        public int $id,
        public int $quantity,
        public bool $preorder,
    )
    {

    }
}
