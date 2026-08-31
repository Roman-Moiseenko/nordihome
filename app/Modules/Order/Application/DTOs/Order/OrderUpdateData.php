<?php

namespace App\Modules\Order\Application\DTOs\Order;

use Spatie\LaravelData\Data;

class OrderUpdateData extends Data
{
    public function __construct(
        public ?string $createdAt = null,
        public ?string $comment = null,
        public ?int $traderId = null,
        public ?int $shopperId = null,

    )
    {
    }
}
