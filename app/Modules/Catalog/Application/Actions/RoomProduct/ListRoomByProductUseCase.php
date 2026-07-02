<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\RoomProduct;

use App\Modules\Catalog\Application\DTOs\Room\RoomProductData;
use App\Modules\Catalog\Application\Interfaces\RoomProductRepositoryInterface;

readonly class ListRoomByProductUseCase
{
    public function __construct(
        private RoomProductRepositoryInterface $roomProductRepository,
    )
    {
    }

    /**
     * @return RoomProductData[]
     */
    public function execute(int $productId): array
    {
        return $this->roomProductRepository->getRoomsByProductId($productId);
    }
}
