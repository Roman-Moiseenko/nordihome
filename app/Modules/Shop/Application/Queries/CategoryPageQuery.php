<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries;

use App\Modules\Shop\Application\DTOs\CategoryViewPageData;
use App\Modules\Shop\Application\DTOs\Parts\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\Parts\CategoryRoomFilterData;
use App\Modules\Shop\Application\DTOs\Parts\ChildrenData;
use App\Modules\Shop\Application\DTOs\Parts\FilterData;
use App\Modules\Shop\Application\DTOs\Parts\IdNameData;
use App\Modules\Shop\Application\DTOs\Parts\ProductCardData;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;
use App\Modules\Shop\Application\DTOs\Parts\UrlData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryPageQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;
use Illuminate\Support\Facades\Cache;

readonly class CategoryPageQuery
{
    public function __construct(
        private CategoryPageQueryRepository $repository,
        private PaginatorBuilder            $paginatorBuilder,
        private SeoAdapter                  $seoAdapter,
    )
    {
    }

    public function execute(string $slug, array $params): ?CategoryViewPageData
    {
        $categoryInfo = $this->repository->getCategory($slug);

        $key_cache = str_replace('{id}', (string)$categoryInfo->id, CacheInvalidationRegistry::CATEGORY_PRODUCTS_ID);

        $perPage = 20;
        $page = (int)($params['page'] ?? 1);


        // Все ID товаров категории после фиьтрации
        //$allProductIds = $this->repository->getProductIdsInCategory($categoryInfo->id);

        $allProductIds = Cache::remember(
            $key_cache,
            now()->addDay(),
            fn() => $this->repository->getProductIdsInCategory($categoryInfo->id),
        );


        $rooms = [];
        if ($allProductIds) {
            $roomsRaw = $this->repository->getRoomsByProductIds($allProductIds, $params);
            $rooms = array_map(
                fn(\stdClass $r) => new ChildrenData(id: (int)$r->id, name: $r->name, slug: $r->slug),
                $roomsRaw,
            );
        }

        $idPaginator = $this->repository->getPaginationProducts(
            $params, $allProductIds, $page, $perPage
        );

        $urlBack = $categoryInfo->parent
            ? new UrlData(
                url: route('shop.category.view', $categoryInfo->parent->slug),
                name: $categoryInfo->parent->name,
            )
            : new UrlData(url: route('shop.category.index'), name: 'Каталог');

        $categoryInfo->totalProducts = $idPaginator->total();

        $productIds = $idPaginator->items();

        $productCardsRaw = $this->repository->loadProductCards($productIds);

        $productCards = array_map(
            fn(array $item) => ProductCardData::fromArray($item),
            $productCardsRaw
        );

        $paginator = $this->paginatorBuilder->build(
            total: $idPaginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->path(),
                'query' => array_diff_key(request()->query(), ['page' => null]),
            ]
        );

        $filters = $this->getCachedFilters($categoryInfo->id);
        $filtersWithOrder = new FilterData(
            minPrice: $filters->minPrice,
            maxPrice: $filters->maxPrice,
            attributes: $filters->attributes,
            brands: $filters->brands,
            tags: $filters->tags,
            sortOrder: $params['order'] ?? '',
            tagId: isset($params['tag_id']) ? (int)$params['tag_id'] : null,
        );

        $meta = $this->seoAdapter->getSeoFromCategoryInfo($categoryInfo);

        return new CategoryViewPageData(
            category: $categoryInfo,
            rooms: $rooms,
            products: $productCards,
            paginator: $paginator,
            filters: $filtersWithOrder,
            meta: new SeoData($meta->title, $meta->description),
            back: $urlBack,
        );
    }

    public function executeNew(array $params): CategoryViewPageData
    {
        $perPage = 20;
        $page = (int)($params['page'] ?? 1);

        $idPaginator = $this->repository->getNewProductIds($params, $page, $perPage);
        $productIds = $idPaginator->items();
        $productCardsRaw = $this->repository->loadProductCards($productIds);

        $productCards = array_map(
            fn(array $item) => ProductCardData::fromArray($item),
            $productCardsRaw
        );

        $paginator = $this->paginatorBuilder->build(
            total: $idPaginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->path(),
                'query' => array_diff_key(request()->query(), ['page' => null]),
            ]
        );

        $filtersWithOrder = new FilterData(
            minPrice: 0,
            maxPrice: 0,
            attributes: [],
            brands: [],
            tags: [],
            sortOrder: $params['order'] ?? '',
            tagId: isset($params['tag_id']) ? (int)$params['tag_id'] : null,
        );

        $categoryInfo = new CategoryRoomFilterData(
            id: 0,
            name: 'Новинки',
            slug: 'novelty',
            totalProducts: $idPaginator->total(),
            children: [],
            parent: null,
        );

        return new CategoryViewPageData(
            category: $categoryInfo,
            rooms: [],
            products: $productCards,
            paginator: $paginator,
            filters: $filtersWithOrder,
            meta: new SeoData('Новинки', 'Новинки Икеа оригинал из Европы с доставкой по всей России. IKEA доступные цены! В наличии в интернет магазине NORDI HOME'),
            back: new UrlData(
                url: route('shop.category.index'),
                name: 'Каталог',
            ),
        );
    }

    private function getCachedFilters(int $categoryId): FilterData
    {
        return Cache::remember(
            "category_filters_{$categoryId}",
            now()->addHours(3),
            function () use ($categoryId) {
                $aggr = $this->repository->getFilterAggregates($categoryId);

                $brands = array_map(
                    fn(\stdClass $item) => new IdNameData(id: (int)$item->id, name: $item->name),
                    $aggr->brands ?? []
                );

                $tags = array_map(
                    fn(\stdClass $item) => new IdNameData(id: (int)$item->id, name: $item->name),
                    $aggr->tags ?? []
                );

                return new FilterData(
                    minPrice: $aggr->min_price ?? 0,
                    maxPrice: $aggr->max_price ?? 0,
                    attributes: $aggr->attributes ?? [],
                    brands: $brands,
                    tags: $tags,
                );
            }
        );
    }
}
