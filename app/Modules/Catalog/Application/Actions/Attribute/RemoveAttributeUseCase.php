<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Attribute;

use App\Modules\Catalog\Domain\Interfaces\AttributeCategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveAttributeUseCase
{
    public function __construct(
        private AttributeRepositoryInterface $attributeRepository,
        private AttributeCategoryRepositoryInterface $attributeCategoryRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.delete')) {
            throw new AccessDeniedException();
        }

        // Отсоединяем все категории атрибута
        $categoryIds = $this->attributeCategoryRepository->getCategoryIdsByAttributeId($id);
        if (!empty($categoryIds)) {
            $this->attributeCategoryRepository->detachCategories($id, $categoryIds);
        }

        // Удаляем атрибут вместе с вариантами (и фото вариантов)
        $this->attributeRepository->delete($id);
    }
}
