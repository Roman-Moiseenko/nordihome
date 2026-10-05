<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\GroupProduct;

use App\Modules\Catalog\Domain\Interfaces\GroupProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class AttachProductsToGroupUseCase
{
    public function __construct(
        private GroupProductRepositoryInterface $groupProductRepository,
    )
    {
    }

    /**
     * Добавить товары к группе (attach — дополняет существующие).
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

        $this->groupProductRepository->attachProducts($groupId, $productIds);
    }
}
