<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Room;

use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ToggleRoomUseCase
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $room = $this->roomRepository->getById($id);

        // Новое значение published (инвертируем текущее)
        $newPublished = !$room->isPublished();

        // Меняем published у самой комнаты
        $room->published = $newPublished;
        $this->roomRepository->save($room);

        // Меняем published у всех дочерних комнат
        $descendantIds = $this->roomRepository->getDescendantIds($id);
        if (!empty($descendantIds)) {
            $this->roomRepository->bulkTogglePublished($descendantIds, $newPublished);
        }
    }
}
