<?php

namespace App\Modules\Order\Application\DTOs\OrderItem;

use Spatie\LaravelData\Data;

class OrderItemUpdateData extends Data
{
    public function __construct(
        public int $id,
        public ?float $sellPrice = null,
        public ?float $discountPercent = null,
        public ?int $quantity = null,
        public ?bool $assemblage = null,
        public ?bool $packing = null,
        public ?string $comment = null,
    ) {
    }
}
