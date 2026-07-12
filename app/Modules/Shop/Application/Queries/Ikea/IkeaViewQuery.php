<?php

namespace App\Modules\Shop\Application\Queries\Ikea;

use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Setting\Entity\Settings;
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
    )
    {
    }

    public function execute(string $slug): IkeaViewPageData
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
            fn(array $item) => $this->getIkeaProductData($item),
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

        //FIXME
        $schema = $this->schemaBuilder->createSchema();

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

    private function getIkeaProductData(ParserProduct $product)
    {

        return new IkeaProductCardData(
            id: $product->id,
            name: $product->name,
            slug: $product->slug,
            code: $product->code,
            price: $product->price_sell, //MAINDO - пересчитать в рубли
            short: $product->short,
            image: ImageInfoData::fromArray($item['image']),
            image_next: ImageInfoData::fromArray($item['image_next']),
        );
    }


}
