<?php

namespace App\Modules\Shop\Application\Queries\Search;

use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Elements\ChildrenData;
use App\Modules\Shop\Application\DTOs\Elements\IdNameData;
use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\PageElements\FilterData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\ProductSearchPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\AttributeQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryPageQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductIndexQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductSearchQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\RoomPageQueryRepository;

readonly class ProductSearchQuery
{
    public function __construct(
        private CategoryPageQueryRepository  $categoryPageQueryRepository,
        private RoomPageQueryRepository      $roomPageQueryRepository,
        private ProductSearchQueryRepository $productSearchQueryRepository,
        private ProductIndexQueryRepository  $productIndexQueryRepository,
        private AttributeQueryRepository     $attributeQueryRepository,
        private PaginatorBuilder             $paginatorBuilder,
    )
    {
    }


    public function execute(string $search, array $params, ClientContext $clientContext): ?ProductSearchPageData
    {
        $allProductIds = $this->productSearchQueryRepository->getProductIdsBySearch($search);
        $perPage = 20;
        $page = (int)($params['page'] ?? 1);

        $rooms = [];
        $categories = [];
        if ($allProductIds) {
            $roomsRaw = $this->categoryPageQueryRepository->getRoomsByProductIds($allProductIds, $params);
            $rooms = array_map(
                fn(\stdClass $r) => new ChildrenData(id: (int)$r->id, name: $r->name, slug: $r->slug),
                $roomsRaw,
            );
            $categoriesRaw = $this->roomPageQueryRepository->getCategoriesByProductIds($allProductIds, $params);
            $categories = array_map(
                fn(\stdClass $r) => new ChildrenData(id: (int)$r->id, name: $r->name, slug: $r->slug),
                $categoriesRaw,
            );
        }

        $idPaginator = $this->productIndexQueryRepository->getFilterSortPaginationProducts($params, $allProductIds, $page, $perPage);
        $productIds = $idPaginator->items();
        $productCardsRaw = $this->productIndexQueryRepository->loadProductCards($productIds, $clientContext);

        $productCards = array_map(
            fn(array $item) => ProductCardData::fromArray($item),
            $productCardsRaw
        );
        $paginator = $this->paginatorBuilder->build(
            total: $idPaginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => '/' . request()->path(),
                'query' => array_diff_key(request()->query(), ['page' => null]),
            ]
        );
        $filters = $this->getFilters(
            array_map(fn(ChildrenData $cat) => $cat->id, $categories),
            $allProductIds,
        );
        $filtersWithOrder = new FilterData(
            minPrice: $filters->minPrice,
            maxPrice: $filters->maxPrice,
            attributes: $filters->attributes,
            brands: $filters->brands,
            tags: $filters->tags,
            sortOrder: $params['order'] ?? '',
            tagId: isset($params['tag_id']) ? (int)$params['tag_id'] : null,
        );
        return new ProductSearchPageData(
            products: $productCards,
            paginator: $paginator,
            filters: $filtersWithOrder,
            meta: new SeoData($search . ' Интернет Магазин', ''),
            search: $search,
        );
    }

    private function getFilters(array $categoryIds, array $productIds): FilterData
    {


        $aggr = $this->attributeQueryRepository->getFilterAggregates($categoryIds, $productIds);

        $tags = array_map(
            fn(\stdClass $item) => new IdNameData(id: (int)$item->id, name: $item->name),
            $aggr->tags ?? []
        );

        return new FilterData(
            minPrice: $aggr->min_price ?? 0,
            maxPrice: $aggr->max_price ?? 0,
            attributes: $aggr->attributes ?? [],
            brands: $aggr->brands ?? [],
            tags: $tags,
        );

    }
}
