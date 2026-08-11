<?php

namespace App\Modules\Catalog\Application\Actions\ProductPrice;

use App\Modules\Catalog\Application\DTOs\ProductPrice\ProductSellPriceData;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Shared\Domain\Entities\UserPermission;

/**
 * Возвращает цену на товар
 * Базовая,
 * Продажная - либо по акции, либо от цены клиента
 */
readonly class GetProductSellPriceUseCase
{

    public function __construct(
        private GetLatestProductPricesUseCase $pricesUseCase,
    ) {
    }
    public function execute(int $id, $priceType): ProductSellPriceData
    {
        //FIXME Переделать на репозиторий, получить ProductEntity c данными по акции

        /** @var Product $product */
        $product = Product::find($id);

        $prices = $this->pricesUseCase->execute($id, new UserPermission(null, [] , ['catalog.product.price.view']));
        $discountId = null;
        $discountType = null;
        if ($product->promotion() != null) {
            $discountId = $product->promotion()->id;
            $discountType = Promotion::class;
            $sellPrice = $product->promotion()->pivot->price;
        } else {
            $sellPrice = $prices[$priceType];
        }


        return new ProductSellPriceData(
            productId: $id,
            basePrice: $prices[PriceType::RETAIL],
            sellPrice: $sellPrice,
            discountId: $discountId,
            discountType: $discountType,
        );
    }
}
