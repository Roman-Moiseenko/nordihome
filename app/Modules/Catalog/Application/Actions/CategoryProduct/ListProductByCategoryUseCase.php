<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListProductByCategoryUseCase
{
    public function __construct(
        private CategoryProductRepositoryInterface $categoryProductRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<ProductCategoryData>
     */
    public function execute(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->categoryProductRepository->getProductsByCategoryId($categoryId, $perPage, $page);
    }
}
