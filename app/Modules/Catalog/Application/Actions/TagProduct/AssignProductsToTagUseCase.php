<?php

namespace App\Modules\Catalog\Application\Actions\TagProduct;

use App\Modules\Catalog\Domain\Interfaces\TagProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

class AssignProductsToTagUseCase
{
    public function __construct(
        private TagProductRepositoryInterface $tagProductRepository,
    )
    {
    }


    /**
     * Назначить товары комнате (sync — заменяет весь набор).
     *
     * @param int   $roomId
     * @param int[] $productIds
     * @param UserPermission $userPermission
     */
    public function execute(int $roomId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $this->tagProductRepository->syncProducts($roomId, $productIds);
    }
}
