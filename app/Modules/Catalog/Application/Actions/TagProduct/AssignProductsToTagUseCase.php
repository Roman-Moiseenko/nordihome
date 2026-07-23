<?php

namespace App\Modules\Catalog\Application\Actions\TagProduct;

use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

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
            throw new \DomainException('Доступ запрещён');
        }

        $this->tagProductRepository->syncProducts($roomId, $productIds);
    }
}
