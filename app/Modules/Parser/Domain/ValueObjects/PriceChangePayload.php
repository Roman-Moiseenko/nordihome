<?php

namespace App\Modules\Parser\Domain\ValueObjects;

final  class PriceChangePayload
{
    public function __construct(
        public float $oldPrice,
        public float $newPrice,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            oldPrice: (float) ($data['old_price'] ?? 0),
            newPrice: (float) ($data['new_price'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'old_price' => $this->oldPrice,
            'new_price' => $this->newPrice,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
