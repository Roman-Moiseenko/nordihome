<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\DTOs\Category\CategoryProductData;
use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;

readonly class ListCategoryByProductUseCase
{
    public function __construct(
        private CategoryProductRepositoryInterface $categoryProductRepository,
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    /**
     * @return CategoryProductData[]
     */
    public function execute(int $productId): array
    {
        $categoryIds = $this->categoryProductRepository->getCategoriesByProductId($productId);

        if (empty($categoryIds)) {
            return [];
        }

        $categories = $this->categoryRepository->findByIds($categoryIds);

        return array_map(
            fn($category) => CategoryProductData::fromEntity($category),
            $categories
        );
    }
}
