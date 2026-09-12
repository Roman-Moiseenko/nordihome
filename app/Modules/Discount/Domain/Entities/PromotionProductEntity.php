<?php

namespace App\Modules\Discount\Domain\Entities;

class PromotionProductEntity
{

    public int $promotionId {
        get => $this->promotionId;
        set => $this->promotionId = $value;
    }
    public int $productId {
        get => $this->productId;
        set => $this->productId = $value;
    }
    public float $price {
        get => $this->price;
        set => $this->price = $value;
    }
    public function __construct(
        int $promotionId,
        int $productId,
        float $price
    )
    {
        $this->promotionId = $promotionId;
        $this->productId = $productId;
        $this->price = $price;
    }
}
