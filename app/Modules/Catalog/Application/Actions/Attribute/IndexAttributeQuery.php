<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Attribute;

use App\Modules\Catalog\Application\DTOs\Attribute\AttributeIndexData;
use App\Modules\Catalog\Application\DTOs\Attribute\CategoryData;
use App\Modules\Catalog\Application\DTOs\Attribute\FilterAttributeIndexData;
use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeCategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexAttributeQuery
{
    public function __construct(
        private AttributeRepositoryInterface $attributeRepository,
        private AttributeCategoryRepositoryInterface $attributeCategoryRepository,
        private AttributeGroupRepositoryInterface $attributeGroupRepository,
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    /**
     * Список атрибутов с пагинацией, фильтрацией, группой и категориями.
     *
     * @return LengthAwarePaginator<AttributeIndexData>
     */
    public function execute(FilterAttributeIndexData &$filter, UserPermission $userPermission): LengthAwarePaginator
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        $paginator = $this->attributeRepository->getFilteredPaginated($filter);

        $attributes = $paginator->getCollection()->all();

        $categoriesMap = $this->buildCategoriesMap($attributes);
        $groupNames = $this->attributeGroupRepository->getNamesByIds(
            array_values(array_unique(array_filter(array_map(
                fn(AttributeEntity $attribute) => $attribute->groupId,
                $attributes,
            )))),
        );

        $paginator->setCollection(
            $paginator->getCollection()->map(
                fn(AttributeEntity $attribute) => AttributeIndexData::fromEntity(
                    $attribute,
                    $attribute->groupId !== null ? ($groupNames[$attribute->groupId] ?? '') : '',
                    $categoriesMap[$attribute->id] ?? [],
                )
            )
        );

        return $paginator;
    }

    /**
     * @param AttributeEntity[] $attributes
     * @return array<int, CategoryData[]>
     */
    private function buildCategoriesMap(array $attributes): array
    {
        $attributeIds = array_map(
            fn(AttributeEntity $attribute) => $attribute->id,
            $attributes,
        );

        $idsByAttribute = $this->attributeCategoryRepository->getCategoryIdsByAttributeIds($attributeIds);

        if (empty($idsByAttribute)) {
            return [];
        }

        $allCategoryIds = array_values(array_unique(array_merge(...array_values($idsByAttribute))));

        $categories = $this->categoryRepository->findByIds($allCategoryIds);
        $byId = [];
        foreach ($categories as $category) {
            $byId[$category->id] = CategoryData::fromEntity($category);
        }

        $map = [];
        foreach ($idsByAttribute as $attributeId => $categoryIds) {
            foreach ($categoryIds as $categoryId) {
                if (isset($byId[$categoryId])) {
                    $map[$attributeId][] = $byId[$categoryId];
                }
            }
        }

        return $map;
    }
}
