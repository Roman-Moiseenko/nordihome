<?php

namespace App\Modules\Shop\Application\DTOs\Elements;

class PromotionProductData
{

    public function __construct(
        public bool $has = false,
        public string $title = '',
        public float $price = 0.0,
        public string $color = '',
        public string $position = '',
        public string $text = '',
        public bool $showTag = true,
        public bool $showDiscount = true,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            has: $data['has'],
            title: $data['title'],
            price: (float)$data['price'],
            color: $data['color'] ?? '',
            position: $data['position'] ?? '',
            text: $data['text'] ?? '',
            showTag: $data['showTag'] ?? true,
            showDiscount: $data['showDiscount'] ?? true,
        );
    }
}
