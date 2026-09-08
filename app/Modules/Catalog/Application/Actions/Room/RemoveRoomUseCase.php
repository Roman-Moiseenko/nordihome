<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Room;

use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveRoomUseCase
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.delete')) throw new AccessDeniedException();

        $this->roomRepository->delete($id);
    }
}
