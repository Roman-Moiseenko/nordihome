<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Application\DTOs\Modification\ModificationCreateSearchData;
use App\Modules\Catalog\Application\DTOs\Product\ProductSearchData;
use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Shared\Application\DTOs\ListNameData;

final readonly class SearchModificationCreateQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private AttributeRepositoryInterface $attributeRepository,
        private ModificationRepositoryInterface $modificationRepository,
    ) {
    }

    /**
     * Данные для диалога создания модификации:
     *  - товары по строке, исключая уже занятые в модификациях;
     *  - атрибуты-варианты (без множественного выбора), которые есть у найденных товаров.
     */
    public function execute(string $query, int $limit = 10): ModificationCreateSearchData
    {
        $usedIds = $this->modificationRepository->getUsedProductIds();

        $entities = $this->productRepository->searchExcluding($query, $usedIds, $limit);

        $products = array_map(
            fn(ProductEntity $entity) => ProductSearchData::fromEntity($entity),
            $entities,
        );

        $productIds = array_map(
            fn(ProductEntity $entity) => $entity->id,
            $entities,
        );

        $attributes = array_map(
            fn(AttributeEntity $entity) => new ListNameData(
                id: $entity->id,
                name: $entity->name,
            ),
            $this->attributeRepository->getModificationAttributesForProducts($productIds),
        );

        return new ModificationCreateSearchData(
            products: $products,
            attributes: $attributes,
        );
    }
}
