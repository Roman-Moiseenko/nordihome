<?php

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListProductByCategoryUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<ProductCategoryData>
     */
    public function execute(int $id, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $paginator = $this->productRepository->findByMainCategoryId($id, $perPage, $page);

        // Заменяем коллекцию сущностей на коллекцию DTO
        $dto = $paginator->getCollection()->map(
            fn(ProductEntity $product) => ProductCategoryData::fromEntity($product)
        );

        $paginator->setCollection($dto);

        return $paginator;
    }
}
