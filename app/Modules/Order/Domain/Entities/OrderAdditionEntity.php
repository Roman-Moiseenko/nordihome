<?php
declare(strict_types=1);

namespace App\Modules\Order\Domain\Entities;

use DateTimeImmutable;

class OrderAdditionEntity
{
    public ?int $id {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $orderId {
        get => $this->orderId;
        set => $this->orderId = $value;
    }

    public float $amount = 0 {
        get => $this->amount;
        set => $this->amount = $value;
    }

    public ?string $comment = null {
        get => $this->comment;
        set => $this->comment = $value;
    }

    public int $additionId {
        get => $this->additionId;
        set => $this->additionId = $value;
    }

    public int $quantity = 1 {
        get => $this->quantity;
        set => $this->quantity = $value;
    }

    public ?DateTimeImmutable $createdAt = null  {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public ?DateTimeImmutable $updatedAt = null  {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public function __construct(
        int $orderId,
        int $additionId,
    ) {
        $this->orderId = $orderId;
        $this->additionId = $additionId;
    }
}
