<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\PromotionProduct;

use App\Modules\Catalog\Application\Interfaces\ProductPriceRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Discount\Application\DTOs\Promotion\PromotionProductViewData;
use App\Modules\Discount\Application\Interfaces\PromotionProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListProductByPromotionUseCase
{
    public function __construct(
        private PromotionProductRepositoryInterface $promotionProductRepository,
        private ProductRepositoryInterface $productRepository,
        private ProductPriceRepositoryInterface $priceRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<PromotionProductViewData>
     */
    public function execute(int $promotionId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $idPaginator = $this->promotionProductRepository->getProductIdsByPromotionId($promotionId, $perPage, $page);

        $items = $idPaginator->getCollection();
        $productIds = $items->pluck('product_id')->toArray();
        $discountPrices = $items->pluck('price', 'product_id')->toArray();

        if (empty($productIds)) {
            return new LengthAwarePaginator(
                items: collect(),
                total: 0,
                perPage: $perPage,
                currentPage: $page,
                options: $idPaginator->getOptions(),
            );
        }

        // Получаем розничные цены товаров (последние по дате)
        $prices = $this->priceRepository->getLatestPricesByType($productIds, PriceType::RETAIL);

        $products = $this->productRepository->findByIds($productIds);

        //FIXME добавить получения кол-во товара на остатках
        $quantities = [];
        $dtoCollection = collect($products)->map(
            fn(ProductEntity $product) => PromotionProductViewData::fromEntity(
                $product,
                (float)($prices[$product->id] ?? 0),
                (float)($discountPrices[$product->id] ?? 0),
                (int)($quantities[$product->id] ?? 0),
            )
        );

        // Сортируем DTO в том же порядке, что и productIds
        $sorted = collect($productIds)->reduce(function ($carry, $id) use ($dtoCollection) {
            $dto = $dtoCollection->firstWhere('id', $id);
            if ($dto) {
                $carry->push($dto);
            }
            return $carry;
        }, collect());

        return new LengthAwarePaginator(
            items: $sorted,
            total: $idPaginator->total(),
            perPage: $idPaginator->perPage(),
            currentPage: $idPaginator->currentPage(),
            options: $idPaginator->getOptions(),
        );
    }
}
