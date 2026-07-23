<?php

namespace App\Modules\Catalog\Application\Actions\TagProduct;

use App\Modules\Catalog\Application\DTOs\Product\ProductRoomData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListProductByTagUseCase
{
    public function __construct(
        private TagProductRepositoryInterface $tagProductRepository,
        private ProductRepositoryInterface $productRepository
    )
    {
    }
    public function execute(int $tagId, int $perPage = 15): LengthAwarePaginator
    {


        $idPaginator = $this->tagProductRepository->getProductIdsByTag($tagId, $perPage);

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
