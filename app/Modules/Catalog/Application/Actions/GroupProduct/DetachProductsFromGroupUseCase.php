<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\GroupProduct;

use App\Modules\Catalog\Domain\Interfaces\GroupProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class DetachProductsFromGroupUseCase
{
    public function __construct(
        private GroupProductRepositoryInterface $groupProductRepository,
    )
    {
    }

    /**
     * Отвязать товары от группы.
     *
     * @param int   $groupId
     * @param int[] $productIds
     * @param UserPermission $userPermission
     */
    public function execute(int $groupId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $this->groupProductRepository->detachProducts($groupId, $productIds);
    }
}
