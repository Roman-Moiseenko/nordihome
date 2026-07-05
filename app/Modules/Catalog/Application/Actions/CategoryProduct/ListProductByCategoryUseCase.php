<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListProductByCategoryUseCase
{
    public function __construct(
        private CategoryProductRepositoryInterface $categoryProductRepository,
        private ProductRepositoryInterface $productRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<ProductCategoryData>
     */
    public function execute(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $idPaginator = $this->categoryProductRepository->getProductIdsByCategoryId($categoryId, $perPage, $page);

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
            ->map(fn($product) => ProductCategoryData::fromEntity($product));

        // Сортируем DTO в том же порядке, что и productIds (findByIds может не сохранять порядок)
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
