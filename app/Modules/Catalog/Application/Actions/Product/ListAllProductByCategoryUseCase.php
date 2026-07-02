<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListAllProductByCategoryUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<ProductCategoryData>
     */
    public function execute(int $id, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->productRepository->findAllByCategoryId($id, $perPage, $page);
    }
}
