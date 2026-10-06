<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\AttributeCategory;

use App\Modules\Catalog\Domain\Interfaces\AttributeCategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class DetachCategoriesFromAttributeUseCase
{
    public function __construct(
        private AttributeCategoryRepositoryInterface $attributeCategoryRepository,
    )
    {
    }

    /**
     * Отвязать категории от атрибута.
     *
     * @param int[] $categoryIds
     */
    public function execute(int $attributeId, array $categoryIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $this->attributeCategoryRepository->detachCategories($attributeId, $categoryIds);
    }
}
