<?php

namespace App\Modules\Order\Application\DTOs\Order;

readonly class OrderIndexData
{
    public function __construct(
        public int $id,
        public float $statusPay,
        public float $statusOut,
        public string $createdAt,
        public string $number,
        public string $clientName,
        public string $clientPhone,
        public float $amount,
        public string $status,
        public string $statusName,
        public string $comment,
        public string $staff,
        public float $refund,

    )
    {
    }
}
