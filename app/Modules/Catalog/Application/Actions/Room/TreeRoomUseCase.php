<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Room;

use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;

readonly class TreeRoomUseCase
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
    )
    {
    }

    /**
     * @return RoomEntity[]
     */
    public function execute(): array
    {
        return $this->roomRepository->getTree();
    }
}
