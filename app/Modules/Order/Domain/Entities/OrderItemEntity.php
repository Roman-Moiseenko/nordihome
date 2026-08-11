<?php
declare(strict_types=1);

namespace App\Modules\Order\Domain\Entities;

use DateTimeImmutable;

class OrderItemEntity
{
    public ?int $id {
        get => $this->id;
        set => $this->id = $value;
    }

    public ?int $orderId {
        get => $this->orderId;
        set => $this->orderId = $value;
    }

    public int $productId {
        get => $this->productId;
        set => $this->productId = $value;
    }

    public float $quantity {
        get => $this->quantity;
        set => $this->quantity = $value;
    }

    public bool $preorder = false {
        get => $this->preorder;
        set => $this->preorder = $value;
    }

    public float $baseCost {
        get => $this->baseCost;
        set => $this->baseCost = $value;
    }

    public float $sellCost {
        get => $this->sellCost;
        set => $this->sellCost = $value;
    }

    public ?int $discountId = null {
        get => $this->discountId;
        set => $this->discountId = $value;
    }

    public ?string $discountType = null  {
        get => $this->discountType;
        set => $this->discountType = $value;
    }

    public bool $fixManual = false {
        get => $this->fixManual;
        set => $this->fixManual = $value;
    }

    public array $options = [] {
        get => $this->options;
        set => $this->options = $value;
    }

    public ?string $comment = null  {
        get => $this->comment;
        set => $this->comment = $value;
    }

    public ?int $reserveId = null {
        get => $this->reserveId;
        set => $this->reserveId = $value;
    }

    public bool $assemblage = false {
        get => $this->assemblage;
        set => $this->assemblage = $value;
    }

    public bool $packing = false {
        get => $this->packing;
        set => $this->packing = $value;
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
        int $productId,
        float $quantity,
        float $baseCost,
        float $sellCost,
    ) {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->baseCost = $baseCost;
        $this->sellCost = $sellCost;
    }
}
