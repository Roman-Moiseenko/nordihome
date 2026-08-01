<?php

declare(strict_types=1);

namespace App\Modules\Parser\Domain\ValueObjects;

class Package
{
    public function __construct(
        public float $height = 0.0,
        public float $width = 0.0,
        public float $length = 0.0,
        public float $weight = 0.0,
        public int $quantity = 1,
    )
    {
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function volume(): float
    {
        $volume = (int)(($this->height * $this->width * $this->length) * 100) / 100;
        return $volume / 1000000;
    }

    public function sides(): float
    {
        return $this->height + $this->length + $this->width;
    }

    public function toArray(): array
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
            'weight' => $this->weight,
            'quantity' => $this->quantity,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            height: (float)($data['height'] ?? 0),
            width: (float)($data['width'] ?? 0),
            length: (float)($data['length'] ?? 0),
            weight: (float)($data['weight'] ?? 0),
            quantity: (int)($data['quantity'] ?? 1),
        );
    }
}
