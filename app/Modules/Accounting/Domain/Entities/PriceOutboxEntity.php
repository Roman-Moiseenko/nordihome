<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

final class PriceOutboxEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $code {
        get => $this->code;
        set => $this->code = $value;
    }

    public int $price {
        get => $this->price;
        set => $this->price = $value;
    }

    public float $priceIkea {
        get => $this->priceIkea;
        set => $this->priceIkea = $value;
    }

    public bool $progress = false {
        get => $this->progress;
        set => $this->progress = $value;
    }

    public function __construct(
        string $code,
        int $price,
        float $priceIkea = 0.0,
    ) {
        $this->code = $code;
        $this->price = $price;
        $this->priceIkea = $priceIkea;
    }

    public function markAsProgress(): void
    {
        $this->progress = true;
    }

    public function resetProgress(): void
    {
        $this->progress = false;
    }

    public function isProgress(): bool
    {
        return $this->progress;
    }
}
