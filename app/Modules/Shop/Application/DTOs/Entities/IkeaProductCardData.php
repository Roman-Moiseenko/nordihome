<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;
use App\Modules\Shop\Application\DTOs\Elements\PromotionProductData;

readonly class IkeaProductCardData
{
    public function __construct(
        public int                  $id,
        public string               $name,
        public string               $slug,
        public string               $code,
        public float                $price,
        public string               $short,
        public ImageInfoData        $image,
        public ImageInfoData        $image_next,

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
