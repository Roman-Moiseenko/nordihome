<?php

namespace App\Modules\Order\Application\DTOs\OrderAddition;

use Spatie\LaravelData\Data;

class OrderAdditionUpdateData extends Data
{
    public function __construct(
        public int $id,
        public ?int $quantity = null,
        public ?int $amount = null,
        public ?string $comment = null,
    ) {
    }
}
