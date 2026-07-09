<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries;

use App\Modules\Page\Repository\MetaTemplateRepository;
use App\Modules\Shop\Application\DTOs\CategoryPageData;
use App\Modules\Shop\Application\DTOs\Parts\CategoryInfo;
use App\Modules\Shop\Application\DTOs\Parts\FilterData;
use App\Modules\Shop\Application\DTOs\Parts\PaginatorData;
use App\Modules\Shop\Application\DTOs\Parts\ProductCard;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryPageQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryTreeQueryRepository;
use Illuminate\Support\Facades\Cache;

class CategoryPageQuery
{
    public function __construct(
        private CategoryPageQueryRepository $repository,
        private CategoryTreeQueryRepository $treeRepo,
        private MetaTemplateRepository $seoService,
    ) {}

    public function execute(string $slug, array $params): ?CategoryPageData
    {
        $category = $this->repository->getCategory($slug);
        if (!$category) return null;

        $categoryInfo = new CategoryInfo(
            id: $category->id,
            name: $category->name,
            slug: $category->slug,
            image: $category->getImage('catalog') ?? '',
            depth: $category->depth ?? 0,
            parentId: $category->parent_id,
        );

        $children = $this->getCachedChildren($category->id);

        $perPage = 20;
        $page = (int)($params['page'] ?? 1);

        // Получаем ID товаров с пагинацией — репозиторий возвращает LengthAwarePaginator
        $idPaginator = $this->repository->getProductIds(
            $params, $category->id, $page, $perPage
        );

        // Загружаем карточки товаров для текущей страницы
        $productIds = $idPaginator->items();
        $productCardsRaw = $this->repository->loadProductCards($productIds);

        // Маппим в DTO
        $productCards = array_map(
            fn(array $item) => new ProductCard(
                id: $item['id'],
                name: $item['name'],
                slug: $item['slug'],
                code: $item['code'],
                price: $item['price'],
                rating: $item['rating'],
                brand: $item['brand'],
                image: $item['image'],
                priority: $item['priority'],
                is_new: $item['is_new'],
                reduced: $item['reduced'],
                only_on_order: $item['only_on_order'],
            ),
            $productCardsRaw
        );

        // Собираем данные пагинации в плоский DTO
        $lastPage = (int)ceil($idPaginator->total() / max($perPage, 1));

        $elements = [];
        for ($i = 1; $i <= $lastPage; $i++) {
            $elements[$i] = url(request()->path()) . '?' . http_build_query(array_merge(request()->query(), ['page' => $i]));
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
                urls: $elements,
                previousPageUrl: $page > 1 ? $elements[$page - 1] : null,
                nextPageUrl: $page < $lastPage ? $elements[$page + 1] : null,
        );
        $filters = $this->getCachedFilters($category->id);
        $meta = $this->seoService->seo($category);

        return new CategoryPageData(
            category: $categoryInfo,
            children: $children,
            products: $productCards,
            paginator: $paginator,
            filters: $filters,
            meta: new SeoData($meta->title, $meta->description),
        );
    }

    private function getCachedChildren(int $parentId): array
    {
        return Cache::remember(
            "category_children_{$parentId}",
            now()->addDay(),
            fn() => $this->treeRepo->getChildren($parentId)
        );
    }

    private function getCachedFilters(int $categoryId): FilterData
    {
        return Cache::remember(
            "category_filters_{$categoryId}",
            now()->addHours(3),
            function () use ($categoryId) {
                $aggr = $this->repository->getFilterAggregates($categoryId);
                return new FilterData(
                    minPrice: $aggr->min_price ?? 0,
                    maxPrice: $aggr->max_price ?? 0,
                    attributes: $aggr->attributes ?? [],
                    brands: $aggr->brands ?? [],
                    tags: $aggr->tags ?? [],
                );
            }
        );
    }
}
