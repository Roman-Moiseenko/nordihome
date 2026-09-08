<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Room;

use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

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
            throw new \DomainException('Доступ запрещён');
        }

        $this->roomRepository->move($id, $position);
    }
}
