<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\RoomProduct;

use App\Modules\Catalog\Application\DTOs\Room\RoomProductData;
use App\Modules\Catalog\Application\Interfaces\RoomProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;

readonly class ListRoomByProductUseCase
{
    public function __construct(
        private RoomProductRepositoryInterface $roomProductRepository,
        private RoomRepositoryInterface $roomRepository,
    )
    {
    }

    /**
     * @return RoomProductData[]
     */
    public function execute(int $productId): array
    {
        $roomIds = $this->roomProductRepository->getRoomsByProductId($productId);

        if (empty($roomIds)) {
            return [];
        }

        $rooms = $this->roomRepository->findByIds($roomIds);

        return array_map(
            fn($room) => RoomProductData::fromEntity($room),
            $rooms
        );
    }
}
