<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Wp;

use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;

readonly class GetRoomByWpIdUseCase
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
    )
    {
    }

    /**
     * Получить комнату по wp_id.
     * Возвращает null, если комната не найдена или содержит дочерние элементы.
     */
    public function execute(int $wpId): ?RoomEntity
    {
        $room = $this->roomRepository->findByWpId($wpId);

        if ($room === null) return null;

        if ($this->roomRepository->hasChildren($room->id)) return null;

        return $room;
    }
}
