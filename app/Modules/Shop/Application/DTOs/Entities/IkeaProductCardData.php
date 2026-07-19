<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;
use App\Modules\Shop\Application\DTOs\Elements\PromotionProductData;

class IkeaProductCardData
{
    public function __construct(
        public readonly int           $id,
        public readonly string        $name,
        public readonly string        $slug,
        public readonly string        $code,
        public float                  $price,
        public readonly string        $short,
        public readonly ImageInfoData $image,
        public readonly ImageInfoData $image_next,

    )
    {
    }

    public static function fromArray(array $item): self
    {
        return new self(
            id: $item['id'],
            name: $item['name'],
            slug: $item['slug'],
            code: $item['code'],
            price: $item['price_sell'],
            short: $item['short'],
            image: ImageInfoData::fromArray($item['image']),
            image_next: ImageInfoData::fromArray($item['image_next']),
        );
    }
}
