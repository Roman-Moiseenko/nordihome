<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Application\DTOs\Modification\ModificationProductSearchData;
use App\Modules\Catalog\Application\DTOs\Product\ProductSearchData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;

final readonly class SearchModificationProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ModificationRepositoryInterface $modificationRepository,
    ) {
    }

    /**
     * Поиск товаров для добавления в модификацию.
     *
     * Товар должен иметь все оси модификации (attributes) и, если заданы
     * фильтры, соответствовать выбранным вариантам ($variantFilters).
     * Уже занятые в любой модификации товары исключаются.
     *
     * @param array<int, int> $variantFilters attribute_id => variant_id
     */
    public function execute(int $modificationId, string $query, array $variantFilters = [], int $limit = 10): ModificationProductSearchData
    {
        $modification = $this->modificationRepository->getById($modificationId);

        $usedIds = $this->modificationRepository->getUsedProductIds();

        $entities = $this->productRepository->searchForModification(
            query: $query,
            attributeIds: $modification->attributes,
            variantFilters: $variantFilters,
            excludeIds: $usedIds,
            limit: $limit,
        );

        $products = array_map(
            fn(ProductEntity $entity) => ProductSearchData::fromEntity($entity),
            $entities,
        );

        return new ModificationProductSearchData(products: $products);
    }
}
