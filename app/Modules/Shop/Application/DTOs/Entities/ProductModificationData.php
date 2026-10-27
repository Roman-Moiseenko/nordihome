<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;

/**
 * Карточка товара-варианта внутри модификации.
 *
 * Передаётся в карточке базового товара (ProductCardData::$modifications)
 * и на странице товара (ProductViewPageData::$modification).
 * Список содержит все товары модификации, базовый (is_primary = true) идёт первым.
 */
class ProductModificationData
{
    public function __construct(
        public int           $id,
        public string        $name,
        public string        $code,
        public string        $slug,
        public ImageInfoData $image,
        public float         $price,
        /** Название варианта атрибута (в порядке осей модификации). */
        public ?string       $variantName = null,
        /** Ссылка на изображение варианта атрибута (AttributeVariant), если есть. */
        public ?string       $variantImage = null,
    )
    {
    }

    public static function fromProductCardData(
        ProductCardData $card,
        ?string $variantName = null,
        ?string $variantImage = null,
    ): self
    {
        return new self(
            id: $card->id,
            name: $card->name,
            code: $card->code,
            slug: $card->slug,
            image: $card->image,
            price: $card->price,
            variantName: $variantName,
            variantImage: $variantImage,
        );
    }
}
