<?php

namespace App\Modules\Shop\Application\Queries\Promotion;

use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Elements\IdNameData;
use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\PageElements\FilterData;
use App\Modules\Shop\Application\DTOs\Pages\ProductIndexPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\AttributeQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\ContentBlockQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductIndexQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\PromotionPageQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;
use Illuminate\Support\Facades\Cache;

readonly class PromotionPageQuery
{
    public function __construct(
        private PromotionPageQueryRepository $repository,
        private PaginatorBuilder            $paginatorBuilder,
        private SeoAdapter                  $seoAdapter,
        private ProductIndexQueryRepository $productIndexQueryRepository,
        private AttributeQueryRepository    $attributeQueryRepository,
        private SchemaBuilder               $schemaBuilder,
        private ContentBlockQueryRepository   $blockRepository,
    )
    {
    }
    public function execute(string $slug, array $params, ClientContext $clientContext): ?ProductIndexPageData
    {
        $mainInfo = $this->repository->getPromotion($slug);
        $key_cache = str_replace('{id}', (string)$mainInfo->id, CacheInvalidationRegistry::PROMOTION_PRODUCTS_ID);

        $perPage = 20;
        $page = (int)($params['page'] ?? 1);
        $allProductIds = Cache::remember(
            $key_cache,
            now()->addDay(),
            fn() => $this->repository->getProductIdsInPromotion($mainInfo->id),
        );
        $idPaginator = $this->productIndexQueryRepository->getFilterSortPaginationProducts($params, $allProductIds, $page, $perPage);

        $mainInfo->totalProducts = $idPaginator->total();
        //TODO secondInfo - категории
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

        //Контент блоки
        $blocks = $this->blockRepository->getBlocksByContainer('promotion', $mainInfo->id);
        // вытаскиваем FAQ из блоков, если есть
        $faq = [];
        foreach ($blocks as $block) {
            if ($block->widget->slug == 'faq') {
                $faq = $block->widget->params['items'];
                break;
            }
        }

        $meta = $this->seoAdapter->getSeo('discount.promotion', $mainInfo, $page);

        $schema = $this->schemaBuilder->buildForProductIndex($productCards, $mainInfo->slug, 'promotion', $faq);
        return new ProductIndexPageData(
            mainInfo: $mainInfo,
            secondInfo: null,
            blocks: $blocks,
            products: $productCards,
            paginator: $paginator,
            filters: $filtersWithOrder,
            meta: $meta,
            schema: $schema,
        );
    }
    private function getCachedFilters(int $categoryId, array $allProductIds): FilterData
    {
        return Cache::remember(
            "promotion_filters_{$categoryId}",
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
