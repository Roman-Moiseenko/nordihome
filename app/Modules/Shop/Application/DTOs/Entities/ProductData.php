<?php

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\DimensionsData;
use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;
use App\Modules\Shop\Application\DTOs\Elements\PromotionProductData;

class ProductData
{

    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public string $code,

        public string $categoryName,
        /** @var ImageInfoData $images */
        public array $images = [],

        public bool $is_wish,
        public int $count_reviews,
        public float $rating,
        public bool $is_sale,
        public PromotionProductData $promotion,
        public float $price,
        public float $public,

        public string $brandLogo,
        public string $brandName,

        public string $description,

        public DimensionsData $dimensions,
        public bool $isRegion,
        public bool $isDelivery,

    )
    {

    }
}
