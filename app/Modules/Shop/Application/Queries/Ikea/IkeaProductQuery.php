<?php

namespace App\Modules\Shop\Application\Queries\Ikea;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Application\Actions\SetRatioPriceUseCase;
use App\Modules\Shop\Application\DTOs\Entities\IkeaProductData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\IkeaIndexPageData;
use App\Modules\Shop\Application\DTOs\Pages\IkeaProductPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\IkeaQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\IkeaTreeQueryRepository;
use Illuminate\Support\Facades\Cache;

readonly class IkeaProductQuery
{
    public function __construct(
        private IkeaTreeQueryRepository $treeRepo,
        private Settings      $settings,
        private SchemaBuilder $schemaBuilder,
        private IkeaQueryRepository $repository,
        private SetRatioPriceUseCase $setRatioPriceUseCase,
    )
    {
    }

    public function execute(string $slug): IkeaProductPageData
    {
        $web = $this->settings->web;

        $categories = Cache::remember(
            CacheInvalidationRegistry::IKEA_CATEGORY_INDEX_PAGE,
            now()->addDay(),
            fn() => $this->treeRepo->getFullTree(),
        );

        $productRaw = $this->repository->getProductByCode($slug);

        $product = IkeaProductData::fromArray($productRaw);
        $product->price = $this->setRatioPriceUseCase->execute($product->price, 'ikea');

        $schema = $this->schemaBuilder->buildForIkeaProduct($product);

        return new IkeaProductPageData(
            categories: $categories,
            product: $product,
            meta: new SeoData(
                title: $web->ikea_title,
                description: $web->ikea_desc,
            ),
            schema: $schema,
            currentId: 0,
        );
    }
}
