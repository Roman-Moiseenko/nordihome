<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class AssignCategoriesToProductUseCase
{
    public function __construct(
        private CategoryProductRepositoryInterface $categoryProductRepository,
    )
    {
    }

    /**
     * Назначить категории товару (sync — заменяет весь набор).
     *
     * @param int   $productId
     * @param int[] $categoryIds
     * @param UserPermission $userPermission
     */
    public function execute(int $productId, array $categoryIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->categoryProductRepository->syncCategories($productId, $categoryIds);
    }
}
