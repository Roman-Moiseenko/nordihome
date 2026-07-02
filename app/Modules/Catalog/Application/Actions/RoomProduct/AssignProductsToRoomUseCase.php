<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\RoomProduct;

use App\Modules\Catalog\Application\Interfaces\RoomProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class AssignProductsToRoomUseCase
{
    public function __construct(
        private RoomProductRepositoryInterface $roomProductRepository,
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
        if (!$userPermission->can('catalog.room.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->roomProductRepository->syncProducts($roomId, $productIds);
    }
}
