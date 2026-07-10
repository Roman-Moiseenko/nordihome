<?php

namespace App\Modules\Shop\Application\DTOs\Parts;

class PromotionProductData
{

    public function __construct(
        public bool $has = false,
        public string $title = '',
        public float $price = 0.0,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            has: $data['has'],
            title: $data['title'],
            price: (float)$data['price'],
        );
    }
}
