<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries\Category;

use App\Modules\Shop\Application\DTOs\Elements\ChildrenData;
use App\Modules\Shop\Application\DTOs\Elements\IdNameData;
use App\Modules\Shop\Application\DTOs\Elements\UrlData;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomSecondData;
use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\PageElements\FilterData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\ProductIndexPageData;
use App\Modules\Shop\Application\Interfaces\BreadcrumbProviderInterface;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\AttributeQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryPageQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductIndexQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;
use Illuminate\Support\Facades\Cache;

readonly class CategoryPageQuery
{
    public function __construct(
        private CategoryPageQueryRepository $repository,
        private PaginatorBuilder            $paginatorBuilder,
        private SeoAdapter                  $seoAdapter,
        private ProductIndexQueryRepository $productIndexQueryRepository,
        private AttributeQueryRepository    $attributeQueryRepository,
        private SchemaBuilder               $schemaBuilder,
    )
    {
    }

    public function execute(string $slug, array $params): ?ProductIndexPageData
    {
        $mainInfo = $this->repository->getCategory($slug);
        if (is_null($mainInfo)) throw new \DomainException("Не найдена категория $slug");

        $key_cache = str_replace('{id}', (string)$mainInfo->id, CacheInvalidationRegistry::CATEGORY_PRODUCTS_ID);

        $perPage = 20;
        $page = (int)($params['page'] ?? 1);


        /**
         * $allProductIds - Список всех ID товаров без фильтрации
         */

        $allProductIds = Cache::remember(
            $key_cache,
            now()->addDay(),
            fn() => $this->repository->getProductIdsInCategory($mainInfo->id),
        );


        $rooms = [];
        if ($allProductIds) {
            $roomsRaw = $this->repository->getRoomsByProductIds($allProductIds, $params);
            $rooms = array_map(
                fn(\stdClass $r) => new ChildrenData(id: (int)$r->id, name: $r->name, slug: $r->slug),
                $roomsRaw,
            );
        }
        $secondInfo = new CategoryRoomSecondData(
            children: $rooms,
            back: new UrlData(url: route('shop.room.index'), name: 'По комнатам'),
            entity: 'room',
        );

        $idPaginator = $this->productIndexQueryRepository->getFilterSortPaginationProducts($params, $allProductIds, $page, $perPage);

        $mainInfo->back = $mainInfo->parent
            ? new UrlData(
                url: route('shop.category.view', $mainInfo->parent->slug),
                name: $mainInfo->parent->name,
            )
            : new UrlData(url: route('shop.category.index'), name: 'Каталог');
        $mainInfo->totalProducts = $idPaginator->total();
        /**
         * $productIds - Список всех ID товаров уже с фильтрацией
         */
        $productIds = $idPaginator->items();

        $productCardsRaw = $this->productIndexQueryRepository->loadProductCards($productIds);

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

        $filters = $this->getCachedFilters($mainInfo->id, $allProductIds);
        $filtersWithOrder = new FilterData(
            minPrice: $filters->minPrice,
            maxPrice: $filters->maxPrice,
            attributes: $filters->attributes,
            brands: $filters->brands,
            tags: $filters->tags,
            sortOrder: $params['order'] ?? '',
            tagId: isset($params['tag_id']) ? (int)$params['tag_id'] : null,
        );

        $meta = $this->seoAdapter->getSeo('catalog.category', $mainInfo);

        $schema = $this->schemaBuilder->buildForProductIndex($productCards, $mainInfo->slug, 'category');
        return new ProductIndexPageData(
            mainInfo: $mainInfo,
            secondInfo: $secondInfo,
            products: $productCards,
            paginator: $paginator,
            filters: $filtersWithOrder,
            meta: $meta,
            schema: $schema,
        );
    }

    public function executeNew(array $params): ProductIndexPageData
    {
        $perPage = 20;
        $page = (int)($params['page'] ?? 1);
        $allProductIds = $this->repository->getNewProductIds();

        $idPaginator = $this->productIndexQueryRepository->getFilterSortPaginationProducts($params, $allProductIds, $page, $perPage);

        $productIds = $idPaginator->items();
        $productCardsRaw = $this->productIndexQueryRepository->loadProductCards($productIds);

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

        $filtersWithOrder = new FilterData(
            minPrice: 0,
            maxPrice: 0,
            attributes: [],
            brands: [],
            tags: [],
            sortOrder: $params['order'] ?? '',
            tagId: isset($params['tag_id']) ? (int)$params['tag_id'] : null,
        );

        $mainInfo = new CategoryRoomMainData(
            id: 0,
            name: 'Новинки',
            slug: 'novelty',
            children: [],
            entity: 'category',
            back: new UrlData(
                url: route('shop.category.index'),
                name: 'Каталог',
            ),
            parent: null,
            totalProducts: $idPaginator->total(),
        );

        $rooms = [];
        if ($productIds) {
            $roomsRaw = $this->repository->getRoomsByProductIds($productIds, $params);
            $rooms = array_map(
                fn(\stdClass $r) => new ChildrenData(id: (int)$r->id, name: $r->name, slug: $r->slug),
                $roomsRaw,
            );
        }
        $secondInfo = new CategoryRoomSecondData(
            children: $rooms,
            back: new UrlData(url: route('shop.room.index'), name: 'По комнатам'),
            entity: 'room',
        );

        //FIXME
        $schema = $this->schemaBuilder->createSchema();
        return new ProductIndexPageData(
            mainInfo: $mainInfo,
            secondInfo: $secondInfo,
            products: $productCards,
            paginator: $paginator,
            filters: $filtersWithOrder,
            meta: new SeoData('Новинки', 'Новинки Икеа оригинал из Европы с доставкой по всей России. IKEA доступные цены! В наличии в интернет магазине NORDI HOME'),
            schema: $schema,
        );
    }

    private function getCachedFilters(int $categoryId, array $allProductIds): FilterData
    {
        return Cache::remember(
            "category_filters_{$categoryId}",
            now()->addDay(),
            function () use ($categoryId, $allProductIds) {
                $aggr = $this->attributeQueryRepository->getFilterAggregates([$categoryId], $allProductIds);


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
        );
    }
}
