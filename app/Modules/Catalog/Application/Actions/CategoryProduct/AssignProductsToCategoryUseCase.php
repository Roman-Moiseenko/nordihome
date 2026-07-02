<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class AssignProductsToCategoryUseCase
{
    public function __construct(
        private CategoryProductRepositoryInterface $categoryProductRepository,
    )
    {
    }

    /**
     * Назначить товары категории (sync — заменяет весь набор).
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

        $this->categoryProductRepository->syncProducts($categoryId, $productIds);
    }
}
