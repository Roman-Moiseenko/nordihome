<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\AttributeCategory;

use App\Modules\Catalog\Application\DTOs\Attribute\CategoryData;
use App\Modules\Catalog\Domain\Interfaces\AttributeCategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;

readonly class ListCategoryByAttributeUseCase
{
    public function __construct(
        private AttributeCategoryRepositoryInterface $attributeCategoryRepository,
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    /**
     * Список категорий, к которым привязан атрибут.
     *
     * @return CategoryData[]
     */
    public function execute(int $attributeId): array
    {
        $categoryIds = $this->attributeCategoryRepository->getCategoryIdsByAttributeId($attributeId);

        if (empty($categoryIds)) {
            return [];
        }

        $categories = $this->categoryRepository->findByIds($categoryIds);

        return array_map(
            fn($category) => CategoryData::fromEntity($category),
            $categories,
        );
    }
}
