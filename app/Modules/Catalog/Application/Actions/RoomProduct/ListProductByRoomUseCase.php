<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\RoomProduct;

use App\Modules\Catalog\Application\DTOs\Product\ProductRoomData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\RoomProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListProductByRoomUseCase
{
    public function __construct(
        private RoomProductRepositoryInterface $roomProductRepository,
        private ProductRepositoryInterface $productRepository
    )
    {
    }

    /**
     * @return LengthAwarePaginator<ProductRoomData>
     */
    public function execute(int $roomId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {


        $idPaginator = $this->roomProductRepository->getProductIdsByRoom($roomId, $perPage, $page);

        $productIds = $idPaginator->getCollection()->pluck('product_id')->toArray();

        $products = $this->productRepository->findByIds($productIds);
        $dtoCollection = collect($products)->map(fn($product) => ProductRoomData::fromEntity($product));
        // Преобразуем каждый элемент в DTO, сохраняя пагинацию
        return new LengthAwarePaginator(
            items: $dtoCollection,
            total: $idPaginator->total(),
            perPage: $idPaginator->perPage(),
            currentPage: $idPaginator->currentPage(),
            options: $idPaginator->getOptions()
        );
    }
}
