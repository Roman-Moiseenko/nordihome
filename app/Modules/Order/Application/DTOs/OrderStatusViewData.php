<?php

namespace App\Modules\Order\Application\DTOs;

readonly class OrderStatusViewData
{

    public function __construct(
        public string $value,
        public ?string $comment,
        public ?string $numberDocument,
        public ?string $dateDocument,
    )
    {

    }
}
