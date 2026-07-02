<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\DTOs\Category\CategoryProductData;
use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;

readonly class ListCategoryByProductUseCase
{
    public function __construct(
        private CategoryProductRepositoryInterface $categoryProductRepository,
    )
    {
    }

    /**
     * @return CategoryProductData[]
     */
    public function execute(int $productId): array
    {
        return $this->categoryProductRepository->getCategoriesByProductId($productId);
    }
}
