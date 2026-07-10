<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries;

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
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryPageQueryRepository;
use Illuminate\Support\Facades\Cache;

class CategoryPageQuery
{
    public function __construct(
        private readonly CategoryPageQueryRepository $repository,
        private readonly MetaTemplateRepository      $seoService,
    ) {}

    public function execute(string $slug, array $params): ?CategoryPageData
    {
        $category = $this->repository->getCategory($slug);
        if (!$category) return null;


        $perPage = 20;
        $page = (int)($params['page'] ?? 1);

        $idPaginator = $this->repository->getProductIds(
            $params, $category->id, $page, $perPage
        );

        if ($category->parent_id === null) {
            $urlBack = new UrlData(
                url: route('shop.category.index'),
                name: 'Каталог',
            );
        } else {
            $urlBack = new UrlData(
                url: route('shop.category.view', $category->parent->slug),
                name: $category->parent->name,
            );
        }

        // Маппим дочерние категории из $category->children
        $children = [];
        foreach ($category->children ?? [] as $child) {
            $children[] = new ChildrenData(
                id: $child->id,
                name: $child->name,
                slug: $child->slug,
            );
        }
        $categoryInfo = new CategoryInfo(
            id: $category->id,
            name: $category->name,
            slug: $category->slug,
            image: $category->getImage('catalog') ?? '',
            depth: $category->depth ?? 0,
            parentId: $category->parent_id,
            totalProducts: $idPaginator->total(),
            children: $children,
        );

        //$children = $this->getCachedChildren($category->id);
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

        $lastPage = (int)ceil($idPaginator->total() / max($perPage, 1));

        $urls = [];
        for ($i = 1; $i <= $lastPage; $i++) {
            $urls[$i] = url(request()->path()) . '?' . http_build_query(array_merge(request()->query(), ['page' => $i]));
        }

        $elements = [];

        if ($lastPage <= 9) {
            $elements[] = array_slice($urls, 0, null, true);
        } else {
            $window = 2;
            $sliderStart = max(2, $page - $window);
            $sliderEnd = min($lastPage - 1, $page + $window);

            $elements[] = [1 => $urls[1]];
            if ($sliderStart > 2) {
                $elements[] = '...';
            }

            $range = [];
            for ($i = $sliderStart; $i <= $sliderEnd; $i++) {
                $range[$i] = $urls[$i];
            }
            $elements[] = $range;

            if ($sliderEnd < $lastPage - 1) {
                $elements[] = '...';
            }
            $elements[] = [$lastPage => $urls[$lastPage]];
        }

        $paginator = new PaginatorData(
            total: $idPaginator->total(),
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage,
            hasPages: $lastPage > 1,
            onFirstPage: $page <= 1,
            hasMorePages: $page < $lastPage,
            elements: $elements,
            url: $urls,
            previousPageUrl: $page > 1 ? $urls[$page - 1] : null,
            nextPageUrl: $page < $lastPage ? $urls[$page + 1] : null,
        );
        $filters = $this->getCachedFilters($category->id);
        $filtersWithOrder = new FilterData(
            minPrice: $filters->minPrice,
            maxPrice: $filters->maxPrice,
            attributes: $filters->attributes,
            brands: $filters->brands,
            tags: $filters->tags,
            sortOrder: $params['order'] ?? '',
            tagId: isset($params['tag_id']) ? (int)$params['tag_id'] : null,
        );
        $meta = $this->seoService->seo($category);

        return new CategoryPageData(
            category: $categoryInfo,
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

        $lastPage = (int)ceil($idPaginator->total() / max($perPage, 1));

        $urls = [];
        for ($i = 1; $i <= $lastPage; $i++) {
            $urls[$i] = url(request()->path()) . '?' . http_build_query(array_merge(request()->query(), ['page' => $i]));
        }

        $elements = [];

        if ($lastPage <= 9) {
            $elements[] = array_slice($urls, 0, null, true);
        } else {
            $window = 2;
            $sliderStart = max(2, $page - $window);
            $sliderEnd = min($lastPage - 1, $page + $window);

            $elements[] = [1 => $urls[1]];
            if ($sliderStart > 2) {
                $elements[] = '...';
            }

            $range = [];
            for ($i = $sliderStart; $i <= $sliderEnd; $i++) {
                $range[$i] = $urls[$i];
            }
            $elements[] = $range;

            if ($sliderEnd < $lastPage - 1) {
                $elements[] = '...';
            }
            $elements[] = [$lastPage => $urls[$lastPage]];
        }

        $paginator = new PaginatorData(
            total: $idPaginator->total(),
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage,
            hasPages: $lastPage > 1,
            onFirstPage: $page <= 1,
            hasMorePages: $page < $lastPage,
            elements: $elements,
            url: $urls,
            previousPageUrl: $page > 1 ? $urls[$page - 1] : null,
            nextPageUrl: $page < $lastPage ? $urls[$page + 1] : null,
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
            depth: 0,
            parentId: null,
            totalProducts: $idPaginator->total(),
            children: [],
        );

        return new CategoryPageData(
            category: $categoryInfo,
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
