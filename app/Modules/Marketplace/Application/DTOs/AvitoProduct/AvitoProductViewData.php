<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use Spatie\LaravelData\Data;

/**
 * Карточка товара Авито (Marketplace/Avito/Show).
 */
class AvitoProductViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $adNumber,
        public readonly ?int $productId,
        public readonly ?string $productCode,
        public readonly ?int $avitoCategoryId,
        public readonly string $categoryPath,
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $basePrice,
        public readonly ?int $sellPrice,
        public readonly int $pricePackage,
        public readonly int $calculatedSellPriceWithDelivery,
        public readonly int $calculatedSellPriceWithoutDelivery,
        public readonly ?string $tnVed,
        public readonly ?string $okpd2,
        public readonly ?string $country,
        public readonly bool $delivery,
        public readonly bool $inStock,
        public readonly bool $active,
        public readonly bool $sale,
    ) {}

    public static function fromEntity(
        AvitoProductEntity $entity,
        ?string $productCode = null,
        string $categoryPath = '',
        int $calculatedSellPriceWithDelivery = 0,
        int $calculatedSellPriceWithoutDelivery = 0,
    ): self {
        return new self(
            id: $entity->id,
            adNumber: $entity->adNumber,
            productId: $entity->productId,
            productCode: $productCode,
            avitoCategoryId: $entity->avitoCategoryId,
            categoryPath: $categoryPath,
            name: $entity->name,
            description: $entity->description,
            basePrice: $entity->basePrice,
            sellPrice: $entity->sellPrice,
            pricePackage: $entity->pricePackage,
            calculatedSellPriceWithDelivery: $calculatedSellPriceWithDelivery,
            calculatedSellPriceWithoutDelivery: $calculatedSellPriceWithoutDelivery,
            tnVed: $entity->tnVed,
            okpd2: $entity->okpd2,
            country: $entity->country,
            delivery: $entity->delivery,
            inStock: $entity->inStock,
            active: $entity->active,
            sale: $entity->sale,
        );
    }
}
