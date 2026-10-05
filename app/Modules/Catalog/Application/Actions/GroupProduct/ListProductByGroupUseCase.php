<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\GroupProduct;

use App\Modules\Catalog\Application\DTOs\Product\ProductGroupData;
use App\Modules\Catalog\Domain\Interfaces\GroupProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListProductByGroupUseCase
{
    public function __construct(
        private GroupProductRepositoryInterface $groupProductRepository,
        private ProductRepositoryInterface      $productRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<ProductGroupData>
     */
    public function execute(int $groupId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $idPaginator = $this->groupProductRepository->getProductIdsByGroupId($groupId, $perPage, $page);

        $productIds = $idPaginator->getCollection()->pluck('product_id')->toArray();

        if (empty($productIds)) {
            return new LengthAwarePaginator(
                items: collect(),
                total: 0,
                perPage: $perPage,
                currentPage: $page,
                options: $idPaginator->getOptions(),
            );
        }

        $products = $this->productRepository->findByIds($productIds);

        $dtoCollection = collect($products)
            ->map(fn($product) => ProductGroupData::fromEntity($product));

        return new LengthAwarePaginator(
            items: $dtoCollection,
            total: $idPaginator->total(),
            perPage: $idPaginator->perPage(),
            currentPage: $idPaginator->currentPage(),
            options: $idPaginator->getOptions(),
        );
    }
}
