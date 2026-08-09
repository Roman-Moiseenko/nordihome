<?php

namespace App\Modules\Order\Application\DTOs;

class ClientOrderData
{
    public function __construct(
        public int $id,
        public int $fullName,
        public string $email,
        public string $phone,
        public string $priceType,
    )
    {
    }
}
