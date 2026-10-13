<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\SizeGrid;

use App\Modules\Catalog\Domain\Interfaces\ProductSizeRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\SizeGridRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveSizeGridUseCase
{
    public function __construct(
        private SizeGridRepositoryInterface $sizeGridRepository,
        private ProductSizeRepositoryInterface $productSizeRepository,
    ) {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.delete')) {
            throw new AccessDeniedException();
        }

        // Очищаем размерный ряд товаров, привязанных к удаляемой сетке
        // (products.size_grid_id обнулит FK на уровне БД — nullOnDelete).
        foreach ($this->productSizeRepository->getProductIdsBySizeGridId($id) as $productId) {
            $this->productSizeRepository->clearForProduct($productId);
        }

        $this->sizeGridRepository->delete($id);
    }
}
