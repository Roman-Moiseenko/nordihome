<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class IndexRoomUseCase
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
    )
    {
    }

    /**
     * @return RoomEntity[]
     */
    public function execute(UserPermission $userPermission): array
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.view')) throw new \DomainException('Доступ запрещён');

        return $this->roomRepository->getAll();
    }
}
