<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries;

use App\Modules\Base\Entity\Meta;
use App\Modules\Page\Repository\MetaTemplateRepository;
use App\Modules\Shop\Application\DTOs\CategoryPageData;
use App\Modules\Shop\Application\DTOs\Parts\CategoryInfo;
use App\Modules\Shop\Application\DTOs\Parts\ChildrenData;
use App\Modules\Shop\Application\DTOs\Parts\FilterData;
use App\Modules\Shop\Application\DTOs\Parts\IdNameData;
use App\Modules\Shop\Application\DTOs\Parts\ImageInfoData;
use App\Modules\Shop\Application\DTOs\Parts\PaginatorData;
use App\Modules\Shop\Application\DTOs\Parts\ProductCardData;
use App\Modules\Shop\Application\DTOs\Parts\PromotionProductData;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;
use App\Modules\Shop\Application\DTOs\Parts\UrlData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryPageQueryRepository;
use Illuminate\Support\Facades\Cache;

class CategoryPageQuery
{
    public function __construct(
        private readonly CategoryPageQueryRepository $repository,
        private readonly MetaTemplateRepository      $seoService,
        private readonly PaginatorBuilder $paginatorBuilder,
    ) {}

    public function execute(string $slug, array $params): ?CategoryPageData
    {
        $categoryInfo = $this->repository->getCategory($slug);



        $perPage = 20;
        $page = (int)($params['page'] ?? 1);

        // Все ID товаров категории после фиьтрации
        $allProductIds = $this->repository->getProductIdsInCategory($categoryInfo->id);
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

        if ($categoryInfo->parent === null) {
            $urlBack = new UrlData(
                url: route('shop.category.index'),
                name: 'Каталог',
            );
        } else {
            $urlBack = new UrlData(
                url: route('shop.category.view', $categoryInfo->slug),
                name: $categoryInfo->name,
            );
        }


        $categoryInfo->totalProducts = $idPaginator->total();

        //$children = $this->getCachedChildren($category->id);
        $productIds = $idPaginator->items();

        $productCardsRaw = $this->repository->loadProductCards($productIds);

        $productCards = array_map(
            fn(array $item) => new ProductCardData(
                id: $item['id'],
                name: $item['name'],
                slug: $item['slug'],
                code: $item['code'],
                price: $item['price'],
                rating: $item['rating'],
                brand: $item['brand'],
                priority: $item['priority'],
                is_new: $item['is_new'],
                reduced: $item['reduced'],
                only_on_order: $item['only_on_order'],
                is_sale: $item['is_sale'],
                count_reviews: $item['count_reviews'],
                price_previous: $item['price_previous'] ?? 0.0,
                quantity: $item['quantity'] ?? 0.0,
                image: ImageInfoData::fromArray($item['images']),
                image_next: ImageInfoData::fromArray($item['images_next']),
                promotion: PromotionProductData::fromArray($item['promotion']),
            ),
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

        //MAINDO !!!!!!!!!!!!!!!!!
        $meta = new Meta(); //$this->seoService->seo($category);

        return new CategoryPageData(
            category: $categoryInfo,
            rooms: $rooms,
            products: $productCards,
            paginator: $paginator,
            filters: $filtersWithOrder,
            meta: new SeoData($meta->title, $meta->description),
            back: $urlBack,
        );
    }

    public function executeNew(array $params): CategoryPageData
    {
        $perPage = 20;
        $page = (int)($params['page'] ?? 1);

        $idPaginator = $this->repository->getNewProductIds($params, $page, $perPage);

        $productIds = $idPaginator->items();

        $productCardsRaw = $this->repository->loadProductCards($productIds);

        // Сортируем карточки по порядку ID из пагинатора
        $sortedCards = [];
        $cardsById = [];
        foreach ($productCardsRaw as $card) {
            $cardsById[$card['id']] = $card;
        }
        foreach ($productIds as $id) {
            if (isset($cardsById[$id])) {
                $sortedCards[] = $cardsById[$id];
            }
        }
        $productCardsRaw = $sortedCards;

        $productCards = array_map(
            fn(array $item) => new ProductCardData(
                id: $item['id'],
                name: $item['name'],
                slug: $item['slug'],
                code: $item['code'],
                price: $item['price'],
                rating: $item['rating'],
                brand: $item['brand'],
                priority: $item['priority'],
                is_new: $item['is_new'],
                reduced: $item['reduced'],
                only_on_order: $item['only_on_order'],
                is_sale: $item['is_sale'],
                count_reviews: $item['count_reviews'],
                price_previous: $item['price_previous'] ?? 0.0,
                quantity: $item['quantity'] ?? 0.0,
                image: ImageInfoData::fromArray($item['images']),
                image_next: ImageInfoData::fromArray($item['images_next']),
                promotion: PromotionProductData::fromArray($item['promotion']),
            ),
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

        $categoryInfo = new CategoryInfo(
            id: 0,
            name: 'Новинки',
            slug: 'novelty',
            image: '',
            totalProducts: $idPaginator->total(),
            children: [],
            parent: null,
        );

        return new CategoryPageData(
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
