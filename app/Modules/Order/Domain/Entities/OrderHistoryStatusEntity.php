<?php

namespace App\Modules\Order\Domain\Entities;

use App\Modules\Order\Domain\ValueObjects\OrderStatus;

class OrderHistoryStatusEntity
{

    public ?int $id {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $orderId {
        get => $this->orderId;
        set => $this->orderId = $value;
    }
    public OrderStatus $value {
        get => $this->value;
        set => $this->value = $value;
    }

    public ?string $numberDocument = null {
        get => $this->numberDocument;
        set => $this->numberDocument = $value;
    }

    public ?string $dateDocument = null {
        get => $this->dateDocument;
        set => $this->dateDocument = $value;
    }

    public \DateTimeImmutable $createdAt {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public function __construct(
        int $orderId,
        OrderStatus $orderStatus,
    )
    {
        $this->orderId = $orderId;
        $this->value = $orderStatus;
    }
}
