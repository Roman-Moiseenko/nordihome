<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Catalog\Application\DTOs\Product\ProductBrandData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ListProductByBrandUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<ProductBrandData>
     */
    public function execute(int $brandId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $paginator = $this->productRepository->findAllByBrandId($brandId, $perPage, $page);

        $dto = $paginator->getCollection()->map(
            fn(ProductEntity $product) => ProductBrandData::fromEntity($product)
        );

        $paginator->setCollection($dto);

        return $paginator;
    }
}
