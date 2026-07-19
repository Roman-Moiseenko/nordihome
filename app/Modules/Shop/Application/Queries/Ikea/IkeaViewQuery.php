<?php

namespace App\Modules\Shop\Application\Queries\Ikea;

use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Application\Actions\SetRatioPriceUseCase;
use App\Modules\Shop\Application\DTOs\Entities\IkeaProductCardData;
use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\IkeaIndexPageData;
use App\Modules\Shop\Application\DTOs\Pages\IkeaViewPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\IkeaQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\IkeaTreeQueryRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

readonly class IkeaViewQuery
{
    public function __construct(
        private IkeaTreeQueryRepository $treeRepo,
        private Settings      $settings,
        private SchemaBuilder $schemaBuilder,
        private PaginatorBuilder            $paginatorBuilder,
        private IkeaQueryRepository $repository,
        private SetRatioPriceUseCase $setRatioPriceUseCase,
    )
    {
    }

    public function execute(string $slug, array $params): IkeaViewPageData
    {
        $web = $this->settings->web;

        $perPage = 20;
        $page = (int)($params['page'] ?? 1);

        $categories = Cache::remember(
            CacheInvalidationRegistry::IKEA_CATEGORY_INDEX_PAGE,
            now()->addDay(),
            fn() => $this->treeRepo->getFullTree(),
        );

        $category = $this->repository->getCategoryBySlug($slug);
        $key_cache = str_replace('{id}', (string)$category->id, CacheInvalidationRegistry::IKEA_PRODUCTS_ID);

        $allProductIds = Cache::remember(
            $key_cache,
            now()->addDay(),
            fn() => $this->repository->getProductIdsInCategory($category->id),
        );


        $idPaginator = $this->repository->getPaginationProducts($allProductIds, $page, $perPage);

        $productCardsRaw = $idPaginator->items();

        $productCards = array_map(
            function (array $product) {
                $prodData = IkeaProductCardData::fromArray($product);
                $prodData->price = $this->setRatioPriceUseCase->execute($product->price, 'ikea');
                return $prodData;
                },
            $productCardsRaw
        );

        $category->totalProducts = $idPaginator->total();
        $paginator = $this->paginatorBuilder->build(
            total: $idPaginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => '/' . request()->path(),
                'query' => array_diff_key(request()->query(), ['page' => null]),
            ]
        );

        $schema = $this->schemaBuilder->buildForProductIndex($productCards, $category->slug, 'ikea');

        return new IkeaViewPageData(
            category: $category,
            categories: $categories,
            products: $productCards,
            paginator: $paginator,
            meta: new SeoData(
                title: $web->ikea_title,
                description: $web->ikea_desc,
            ),
            schema: $schema,

        );
    }

}
