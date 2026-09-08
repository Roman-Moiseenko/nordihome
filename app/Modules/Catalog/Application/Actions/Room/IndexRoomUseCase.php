<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Room;

use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

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
        if (!$userPermission->can('catalog.category.view')) throw new AccessDeniedException();

        return $this->roomRepository->getAll();
    }
}
