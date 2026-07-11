<?php

namespace App\Modules\Shop\Application\Queries\Room;

use App\Modules\Shop\Application\DTOs\ProductIndexPageData;
use App\Modules\Shop\Application\DTOs\Parts\ChildrenData;
use App\Modules\Shop\Application\DTOs\Parts\FilterData;
use App\Modules\Shop\Application\DTOs\Parts\ProductCardData;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;
use App\Modules\Shop\Application\DTOs\Parts\UrlData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\RoomPageQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;
use Illuminate\Support\Facades\Cache;

class RoomPageQuery
{
    public function __construct(
        private RoomPageQueryRepository $repository,
        private PaginatorBuilder            $paginatorBuilder,
        private SeoAdapter                  $seoAdapter,
    )
    {
    }

    public function execute(string $slug, array $params): ?ProductIndexPageData
    {
        $mainInfo = $this->repository->getRoom($slug);

        $key_cache = str_replace('{id}', (string)$mainInfo->id, CacheInvalidationRegistry::ROOM_PRODUCTS_ID);

        $perPage = 20;
        $page = (int)($params['page'] ?? 1);


        $allProductIds = Cache::remember(
            $key_cache,
            now()->addDay(),
            fn() => $this->repository->getProductIdsInRoom($mainInfo->id),
        );


        $categories = [];
        if ($allProductIds) {
            $categoriesRaw = $this->repository->getCategoriesByProductIds($allProductIds, $params);
            $categories = array_map(
                fn(\stdClass $r) => new ChildrenData(id: (int)$r->id, name: $r->name, slug: $r->slug),
                $categoriesRaw,
            );
        }


        $idPaginator = $this->repository->getPaginationProducts(
            $params, $allProductIds, $page, $perPage
        );

        $urlBack = $mainInfo->parent
            ? new UrlData(
                url: route('shop.room.view', $mainInfo->parent->slug),
                name: $mainInfo->parent->name,
            )
            : new UrlData(url: route('shop.room.index'), name: 'Комнаты');

        $mainInfo->totalProducts = $idPaginator->total();

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

        $filters = $this->getCachedFilters($mainInfo->id);
        $filtersWithOrder = new FilterData(
            minPrice: $filters->minPrice,
            maxPrice: $filters->maxPrice,
            attributes: $filters->attributes,
            brands: $filters->brands,
            tags: $filters->tags,
            sortOrder: $params['order'] ?? '',
            tagId: isset($params['tag_id']) ? (int)$params['tag_id'] : null,
        );

        $meta = $this->seoAdapter->getSeoFromCategoryInfo($mainInfo);

        return new ProductIndexPageData(
            mainInfo: $mainInfo,
            rooms: $categories,
            products: $productCards,
            paginator: $paginator,
            filters: $filtersWithOrder,
            meta: new SeoData($meta->title, $meta->description),
            back: $urlBack,
        );
    }
}
