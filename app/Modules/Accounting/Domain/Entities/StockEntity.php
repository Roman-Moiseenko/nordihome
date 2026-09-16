<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use DateTimeImmutable;

final class StockEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $productId {
        get => $this->productId;
        set => $this->productId = $value;
    }

    public int $quantity {
        get => $this->quantity;
        set => $this->quantity = $value;
    }

    public int $reserve {
        get => $this->reserve;
        set => $this->reserve = $value;
    }

    public ?DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public function __construct(
        int $productId,
        int $quantity = 0,
        int $reserve = 0,
    ) {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->reserve = $reserve;
    }

    public function isEmpty(): bool
    {
        return $this->quantity === 0;
    }
}
