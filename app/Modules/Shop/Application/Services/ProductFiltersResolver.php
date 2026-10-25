<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Services;

use App\Modules\Shop\Application\DTOs\Elements\IdNameData;
use App\Modules\Shop\Application\DTOs\PageElements\FilterProductsData;
use App\Modules\Shop\Infrastructure\Persistence\Query\AttributeQueryRepository;

/**
 * Собирает данные фильтров списка товаров (агрегаты цен/брендов/тегов/атрибутов)
 * и превращает их в DTO для фронтенда.
 */
readonly class ProductFiltersResolver
{
    public function __construct(
        private AttributeQueryRepository $attributeQueryRepository,
    )
    {
    }

    /**
     * @param int[] $categoryIds
     * @param int[] $productIds
     * @return object{min_price: float, max_price: float, brands: array, tags: array, attributes: array}
     */
    public function resolveAggregates(array $categoryIds, array $productIds): object
    {
        return $this->attributeQueryRepository->getFilterAggregates($categoryIds, $productIds);
    }

    /**
     * @param object{min_price?: float, max_price?: float, brands?: array, tags?: array, attributes?: array} $aggregates
     * @param array $params
     */
    public function buildFilterProductsData(object $aggregates, array $params): FilterProductsData
    {
        $tags = array_map(
            fn(\stdClass $item) => new IdNameData(id: (int)$item->id, name: $item->name),
            $aggregates->tags ?? []
        );

        return new FilterProductsData(
            minPrice: $aggregates->min_price ?? 0,
            maxPrice: $aggregates->max_price ?? 0,
            attributes: $aggregates->attributes ?? [],
            brands: $aggregates->brands ?? [],
            tags: $tags,
            sortOrder: $params['order'] ?? '',
            tagId: isset($params['tag_id']) ? (int)$params['tag_id'] : null,
        );
    }
}
