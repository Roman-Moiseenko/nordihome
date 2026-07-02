<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\RoomProduct;

use App\Modules\Catalog\Application\DTOs\Product\ProductRoomData;
use App\Modules\Catalog\Application\Interfaces\RoomProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListProductByRoomUseCase
{
    public function __construct(
        private RoomProductRepositoryInterface $roomProductRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<ProductRoomData>
     */
    public function execute(int $roomId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->roomProductRepository->getProductsByRoomId($roomId, $perPage, $page);
    }
}
