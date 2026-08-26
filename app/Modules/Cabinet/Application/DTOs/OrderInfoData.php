<?php

namespace App\Modules\Cabinet\Application\DTOs;

class OrderInfoData
{
    public function __construct(
        public string $date,
        public string $number,
        public float $totalAmount,
        public string $status,
        public string $statusName,
        public float $delivery,
        public string $address,
    )
    {

    }
}
