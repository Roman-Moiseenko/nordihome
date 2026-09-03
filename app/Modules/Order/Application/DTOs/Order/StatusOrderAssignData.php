<?php

namespace App\Modules\Order\Application\DTOs\Order;

use App\Modules\Order\Domain\ValueObjects\OrderStatus;

class StatusOrderAssignData
{
    public function __construct(
        public int $orderId,
        public OrderStatus $status,
        public ?string     $comment = null,
        public ?string     $numberDocument = null,
        public ?string     $dateDocument = null,
    )
    {

    }
}
