<?php

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListProductByCategoryUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<ProductCategoryData>
     */
    public function execute(int $id, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->productRepository->findByCategoryId($id, $perPage, $page);
    }
}
