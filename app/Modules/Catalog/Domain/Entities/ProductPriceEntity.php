<?php

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Catalog\Domain\ValueObjects\PriceType;

final class ProductPriceEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $productId {
        get => $this->productId;
        set => $this->productId = $value;
    }
    public PriceType $priceType {
        get => $this->priceType;
        set => $this->priceType = $value;
    }
    public float $price {
        get => $this->price;
        set => $this->price = $value;
    }

    public \DateTimeImmutable $setAt {
        get => $this->setAt;
        set => $this->setAt = $value;
    }
    public ?string $founded = null {
        get => $this->founded;
        set => $this->founded = $value;
    }
    public ?string $comment = null {
        get => $this->comment;
        set => $this->comment = $value;
    }

    public function __construct(
        int $productId,
        float $price,
        PriceType $priceType,
        ?\DateTimeImmutable $setAt = null,
    )
    {
        $this->productId = $productId;
        $this->price = $price;
        $this->priceType = $priceType;

        $this->setAt = $setAt ?? new \DateTimeImmutable();

    }

}
