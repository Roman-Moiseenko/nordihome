<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\RoomProduct;

use App\Modules\Catalog\Application\Interfaces\RoomProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class AttachRoomsToProductUseCase
{
    public function __construct(
        private RoomProductRepositoryInterface $roomProductRepository,
    )
    {
    }

    /**
     * Добавить комнаты к товару (attach — дополняет существующие).
     *
     * @param int   $productId
     * @param int[] $roomIds
     * @param UserPermission $userPermission
     */
    public function execute(int $productId, array $roomIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->roomProductRepository->attachRooms($productId, $roomIds);
    }
}
