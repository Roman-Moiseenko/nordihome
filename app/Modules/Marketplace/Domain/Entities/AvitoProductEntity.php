<?php

namespace App\Modules\Marketplace\Domain\Entities;

final class AvitoProductEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public ?string $adId = null {
        get => $this->adId;
        set => $this->adId = $value;
    }

    public ?string $adNumber = null {
        get => $this->adNumber;
        set => $this->adNumber = $value;
    }

    public ?int $productId = null {
        get => $this->productId;
        set => $this->productId = $value;
    }

    public ?int $avitoCategoryId = null {
        get => $this->avitoCategoryId;
        set => $this->avitoCategoryId = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public ?string $description = null {
        get => $this->description;
        set => $this->description = $value;
    }

    public int $price = 0 {
        get => $this->price;
        set => $this->price = $value;
    }

    public int $pricePackage = 0 {
        get => $this->pricePackage;
        set => $this->pricePackage = $value;
    }

    public ?string $tnVed = null {
        get => $this->tnVed;
        set => $this->tnVed = $value;
    }

    public ?string $okpd2 = null {
        get => $this->okpd2;
        set => $this->okpd2 = $value;
    }

    public bool $delivery = false {
        get => $this->delivery;
        set => $this->delivery = $value;
    }

    public bool $active = false {
        get => $this->active;
        set => $this->active = $value;
    }

    public function __construct(
        string $name,
        ?int $productId = null,
        ?int $avitoCategoryId = null,
    ) {
        $this->name = $name;
        $this->productId = $productId;
        $this->avitoCategoryId = $avitoCategoryId;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
