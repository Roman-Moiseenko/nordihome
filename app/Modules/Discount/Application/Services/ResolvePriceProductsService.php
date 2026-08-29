<?php

namespace App\Modules\Discount\Application\Services;

use App\Modules\Catalog\Application\Interfaces\ProductPriceRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Discount\Application\Interfaces\PromotionProductRepositoryInterface;
use App\Modules\Discount\Application\Interfaces\PromotionRepositoryInterface;

readonly class ResolvePriceProductsService
{
    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
        private ProductPriceRepositoryInterface $priceRepository,
    )
    {
    }
    /**
     * Для товаров без цены (0 или null) вычисляем цену со скидкой
     * на основе розничной цены и процента скидки акции.
     *
     * @param array<int, float> $products [product_id => price]
     * @return array<int, float> [product_id => price]
     */
    public function execute(int $promotionId, array $products): array
    {
        if (empty($products)) return $products;

        $promotion = $this->promotionRepository->getById($promotionId);
        $discount = $promotion->discount;

        $prices = $this->priceRepository->getLatestPricesByType(
            array_keys($products),
            PriceType::RETAIL
        );

        foreach ($products as $productId => $price) {
            if (empty($price)) {
                $retail = (float) ($prices[$productId] ?? 0);
                $products[$productId] = $discount > 0
                    ? ceil($retail - ($retail * $discount / 100))
                    : $retail;
            }
        }

        return $products;
    }
}
