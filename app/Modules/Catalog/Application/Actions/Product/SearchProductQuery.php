<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Catalog\Application\DTOs\Product\ProductSearchData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;

final readonly class SearchProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    /**
     * Поиск товаров по строке.
     *
     * @return ProductSearchData[]
     */
    public function execute(string $query, int $limit = 10): array
    {
        return array_map(
            fn(ProductEntity $entity) => ProductSearchData::fromEntity($entity),
            $this->productRepository->search($query, $limit),
        );
    }
}
