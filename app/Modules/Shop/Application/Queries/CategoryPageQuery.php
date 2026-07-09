<?php

namespace App\Modules\Shop\Application\Queries;

use App\Modules\Page\Repository\MetaTemplateRepository;
use App\Modules\Shop\Application\DTOs\CategoryPageData;
use App\Modules\Shop\Application\DTOs\Parts\CategoryInfo;
use App\Modules\Shop\Application\DTOs\Parts\FilterData;
use App\Modules\Shop\Application\DTOs\Parts\ProductCardPage;
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

        $productIdsResult = $this->repository->getProductIds(
            $params, $category->id, (int)($params['page'] ?? 1), 20
        );

        $productCards = $this->repository->loadProductCards($productIdsResult['ids']);

        $filters = $this->getCachedFilters($category->id);
        $meta = $this->seoService->seo($category);

        return new CategoryPageData(
            category: $categoryInfo,
            children: $children,
            products: new ProductCardPage($productCards, $productIdsResult['total']),
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
