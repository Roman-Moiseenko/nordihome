<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Attribute;

use App\Modules\Catalog\Application\DTOs\Attribute\AttributeViewData;
use App\Modules\Catalog\Application\DTOs\Attribute\CategoryData;
use App\Modules\Catalog\Domain\Interfaces\AttributeCategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewAttributeQuery
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
     * Карточка атрибута для страницы Catalog/Attribute/Show.
     */
    public function execute(int $id, UserPermission $userPermission): AttributeViewData
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        $attribute = $this->attributeRepository->getById($id);

        // Название группы
        $group = '';
        if ($attribute->groupId !== null) {
            $groupNames = $this->attributeGroupRepository->getNamesByIds([$attribute->groupId]);
            $group = $groupNames[$attribute->groupId] ?? '';
        }

        // Привязанные категории
        $categories = [];
        $categoryIds = $this->attributeCategoryRepository->getCategoryIdsByAttributeId($id);
        if (!empty($categoryIds)) {
            foreach ($this->categoryRepository->findByIds($categoryIds) as $category) {
                $categories[] = CategoryData::fromEntity($category);
            }
        }

        return AttributeViewData::fromEntity($attribute, $group, $categories);
    }
}
