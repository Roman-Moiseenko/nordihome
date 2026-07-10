<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Parts;

readonly class ProductCardData
{
    public function __construct(
        public int                  $id,
        public string               $name,
        public string               $slug,
        public string               $code,
        public float                $price,
        public float                $rating,
        public string               $brand,
        public bool                 $priority,
        public bool                 $is_new,
        public bool                 $reduced,
        public bool                 $only_on_order,
        public bool                 $is_sale = true,
        public string               $count_reviews = '0 отзывов',
        public float                $price_previous = 0.0,
        public float                $quantity = 0.0,
        public ImageInfoData        $image,
        public ImageInfoData        $image_next,
        public PromotionProductData $promotion = new PromotionProductData(),
    )
    {
    }
}
