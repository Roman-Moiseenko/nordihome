<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class DetachProductFromCategoryUseCase
{
    public function __construct(
        private CategoryProductRepositoryInterface $categoryProductRepository,
    )
    {
    }

    /**
     * Отвязать товары от категории.
     *
     * @param int   $categoryId
     * @param int[] $productIds
     * @param UserPermission $userPermission
     */
    public function execute(int $categoryId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.category.update')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->categoryProductRepository->detachProducts($categoryId, $productIds);
    }
}
