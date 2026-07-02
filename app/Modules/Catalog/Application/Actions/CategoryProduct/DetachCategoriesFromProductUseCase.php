<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class DetachCategoriesFromProductUseCase
{
    public function __construct(
        private CategoryProductRepositoryInterface $categoryProductRepository,
    )
    {
    }

    /**
     * Отвязать категории от товара.
     *
     * @param int   $productId
     * @param int[] $categoryIds
     * @param UserPermission $userPermission
     */
    public function execute(int $productId, array $categoryIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.update')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->categoryProductRepository->detachCategories($productId, $categoryIds);
    }
}
