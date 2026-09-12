<?php
declare(strict_types=1);

namespace App\Modules\Cart\Domain\Entities;

class CartItemEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }
    public int $productId  {
        get => $this->productId;
        set => $this->productId = $value;
    }

    public bool $isParser  {
        get => $this->isParser;
        set => $this->isParser = $value;
    }
    public bool $check = true  {
        get => $this->check;
        set => $this->check = $value;
    }
    public float $quantity  {
        get => $this->quantity;
        set => $this->quantity = $value;
    }

    public function __construct(
        int $productId, float $quantity, bool $isParser
    )
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->isParser = $isParser;
    }

}

