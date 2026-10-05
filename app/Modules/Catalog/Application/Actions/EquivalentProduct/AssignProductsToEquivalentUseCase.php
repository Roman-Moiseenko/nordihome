<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\EquivalentProduct;

use App\Modules\Catalog\Domain\Interfaces\EquivalentProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class AssignProductsToEquivalentUseCase
{
    public function __construct(
        private EquivalentProductRepositoryInterface $equivalentProductRepository,
    )
    {
    }

    /**
     * Назначить товары группе аналогов (sync — заменяет весь набор).
     *
     * @param int   $equivalentId
     * @param int[] $productIds
     * @param UserPermission $userPermission
     */
    public function execute(int $equivalentId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $this->equivalentProductRepository->syncProducts($equivalentId, $productIds);
    }
}
