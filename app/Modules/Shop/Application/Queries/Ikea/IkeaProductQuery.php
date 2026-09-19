<?php

namespace App\Modules\Shop\Application\Queries\Ikea;

use App\Modules\Setting\Application\Actions\GetWebSettingsUseCase;
use App\Modules\Shop\Application\Actions\SetRatioPriceUseCase;
use App\Modules\Shop\Application\DTOs\Entities\IkeaProductData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\IkeaProductPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\IkeaQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\IkeaTreeQueryRepository;
use App\Modules\Storefront\Infrastructure\Persistence\CacheInvalidationRegistry;
use Illuminate\Support\Facades\Cache;

readonly class IkeaProductQuery
{
    public function __construct(
        private IkeaTreeQueryRepository $treeRepo,
        private GetWebSettingsUseCase $webSettingsUseCase,
        private SchemaBuilder $schemaBuilder,
        private IkeaQueryRepository $repository,
        private SetRatioPriceUseCase $setRatioPriceUseCase,
    )
    {
    }

    public function execute(string $slug): IkeaProductPageData
    {
        $web = $this->webSettingsUseCase->execute();

        $categories = Cache::remember(
            CacheInvalidationRegistry::IKEA_CATEGORY_INDEX_PAGE,
            now()->addDay(),
            fn() => $this->treeRepo->getFullTree(),
        );

        $productRaw = $this->repository->getProductByCode($slug);

        $product = IkeaProductData::fromArray($productRaw);
        $product->price = $this->setRatioPriceUseCase->execute($product->price, 'ikea');

        $schema = $this->schemaBuilder->buildForIkeaProduct($product);
        $meta = new SeoData(
            title: $product->name . ' ' . $product->code,
            description: $product->short,
            canonical: route('shop.ikea.product', $slug),
            ogSiteName: $web->web_name,
        );
        //$meta->ogImages[] = OgImage::fromData()

        return new IkeaProductPageData(
            categories: $categories,
            product: $product,
            meta: $meta,
            schema: $schema,
            currentId: 0,
        );
    }
}
