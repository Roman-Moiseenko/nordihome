<?php

namespace App\Modules\Order\Application\Queries;

use App\Modules\Accounting\Application\Actions\ProductPrice\GetLatestPricesQuery;
use App\Modules\Accounting\Application\DTOs\ProductPrice\ProductSellPriceData;
use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Discount\Application\Actions\PromotionProduct\GetPromotionDataByProductUseCase;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Shared\Domain\Entities\UserPermission;

/**
 * Возвращает цену на товар
 * Базовая,
 * Продажная - либо по акции, либо от цены клиента
 * Использует запросы с двух модулей - Accounting и Discount
 */
readonly class GetSellPriceQuery
{

    public function __construct(
        private GetLatestPricesQuery             $pricesUseCase,
        private GetPromotionDataByProductUseCase $promotionDataByProductUseCase,
    ) {
    }
    public function execute(int $id, PriceType $priceType): ProductSellPriceData
    {
        $prices = $this->pricesUseCase->execute($id, new UserPermission(null, [] , ['accounting.price.view']));

        $discountId = null;
        $discountType = null;
        $discountName = null;
        $promotion = $this->promotionDataByProductUseCase->execute($id);
        if ($promotion != null) {
            $discountId = $promotion->id;
            $discountType = Promotion::class;
            $sellPrice = $promotion->price;
            $discountName = $promotion->name;
        } else {
            $sellPrice = $prices[$priceType->value] ?? $prices[PriceType::RETAIL];
        }


        return new ProductSellPriceData(
            productId: $id,
            basePrice: $prices[PriceType::RETAIL],
            sellPrice: $sellPrice,
            discountId: $discountId,
            discountType: $discountType,
            discountName: $discountName
        );
    }
}
