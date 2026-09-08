<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\RoomProduct;

use App\Modules\Catalog\Domain\Interfaces\RoomProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class DetachProductsFromRoomUseCase
{
    public function __construct(
        private RoomProductRepositoryInterface $roomProductRepository,
    )
    {
    }

    /**
     * Отвязать товары от комнаты.
     *
     * @param int   $roomId
     * @param int[] $productIds
     * @param UserPermission $userPermission
     */
    public function execute(int $roomId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.category.edit')) {
            throw new AccessDeniedException();
        }

        $this->roomProductRepository->detachProducts($roomId, $productIds);
    }
}
