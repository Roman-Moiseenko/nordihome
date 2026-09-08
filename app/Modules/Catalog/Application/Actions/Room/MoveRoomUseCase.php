<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Room;

use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class MoveRoomUseCase
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
    )
    {
    }

    public function execute(int $id, int $position, UserPermission $userPermission): void
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.edit')) {
            throw new AccessDeniedException();
        }

        $this->roomRepository->move($id, $position);
    }
}
