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

    public int $retail {
        get => $this->retail;
        set => $this->retail = $value;
    }

    public float $sellIkea {
        get => $this->sellIkea;
        set => $this->sellIkea = $value;
    }

    public int $bulk = 0 {
        get => $this->bulk;
        set => $this->bulk = $value;
    }

    public bool $progress = false {
        get => $this->progress;
        set => $this->progress = $value;
    }

    public function __construct(
        string $code,
        int $retail,
        float $sellIkea = 0.0,
        int $bulk = 0,
    ) {
        $this->code = $code;
        $this->retail = $retail;
        $this->sellIkea = $sellIkea;
        $this->bulk = $bulk;
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
