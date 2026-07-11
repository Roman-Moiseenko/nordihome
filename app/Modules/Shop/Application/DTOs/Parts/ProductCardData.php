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
        public float                $rating, //рейтинг товара
        public string               $brand,
        public bool                 $priority, //приоритетный показ
        public bool                 $is_new, // новый
        public bool                 $reduced, //цена снижена
        public bool                 $only_on_order, //только под заказ
        public bool                 $is_sale = true,  //удалить .....
        public string               $count_reviews = '0 отзывов',
        public float                $price_previous = 0.0,
        public float                $quantity = 0.0,
        public ImageInfoData        $image,
        public ImageInfoData        $image_next,
        public PromotionProductData $promotion = new PromotionProductData(),
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
            price: $item['price'],
            rating: $item['rating'],
            brand: $item['brand'],
            priority: $item['priority'],
            is_new: $item['is_new'],
            reduced: $item['reduced'],
            only_on_order: $item['only_on_order'],
            is_sale: $item['is_sale'],
            count_reviews: $item['count_reviews'],
            price_previous: $item['price_previous'] ?? 0.0,
            quantity: $item['quantity'] ?? 0.0,
            image: ImageInfoData::fromArray($item['image']),
            image_next: ImageInfoData::fromArray($item['image_next']),
            promotion: PromotionProductData::fromArray($item['promotion']),
        );
    }
}
