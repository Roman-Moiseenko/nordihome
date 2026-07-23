<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\RoomProduct;

use App\Modules\Catalog\Application\Interfaces\RoomProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class AttachProductsToRoomUseCase
{
    public function __construct(
        private RoomProductRepositoryInterface $roomProductRepository,
    )
    {
    }

    /**
     * Добавить товары к комнате (attach — дополняет существующие).
     *
     * @param int   $roomId
     * @param int[] $productIds
     * @param UserPermission $userPermission
     */
    public function execute(int $roomId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.category.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->roomProductRepository->attachProducts($roomId, $productIds);
    }
}
