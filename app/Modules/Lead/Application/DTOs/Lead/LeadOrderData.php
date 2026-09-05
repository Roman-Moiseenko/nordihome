<?php

namespace App\Modules\Lead\Application\DTOs\Lead;

use App\Modules\Order\Domain\Entities\OrderEntity;
use Spatie\LaravelData\Data;

class LeadOrderData extends Data
{
    public function __construct(
        public int $id,
        public ?int $number,
        public float $amount,
        /** @var LeadOrderItemData[] $products */
        public array $products,
    )
    {
    }

    public static function fromEntity(OrderEntity $orderEntity): self
    {
        foreach ($orderEntity->items as $item) {

        }

        return new self(
            id: $orderEntity->id,
            number: $orderEntity->number,
            amount: $orderEntity->getTotalAmount(),
            products: [],
        );
    }
}
