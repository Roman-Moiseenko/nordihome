<?php

namespace App\Modules\Shop\Application\Queries\Ikea;

use App\Modules\Setting\Application\Actions\GetWebSettingsUseCase;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\IkeaIndexPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\IkeaTreeQueryRepository;
use App\Modules\Storefront\Infrastructure\Persistence\CacheInvalidationRegistry;
use Illuminate\Support\Facades\Cache;

readonly class IkeaIndexQuery
{
    public function __construct(
        private IkeaTreeQueryRepository $treeRepo,
        private GetWebSettingsUseCase $webSettingsUseCase,
        private SchemaBuilder $schemaBuilder,
    )
    {
    }

    public function execute(): IkeaIndexPageData
    {
        $web = $this->webSettingsUseCase->execute();

        $categories = Cache::remember(
            CacheInvalidationRegistry::IKEA_CATEGORY_INDEX_PAGE,
            now()->addDay(),
            fn() => $this->treeRepo->getFullTree(),
        );

        $schema = $this->schemaBuilder->buildForCategoryIndex($categories, 'ikea');

        $meta =new SeoData(
            title: $web->ikea_title,
            description: $web->ikea_desc,
            canonical: route('shop.ikea.index'),
            ogSiteName: $web->web_name,
        );
        return new IkeaIndexPageData(
            meta: $meta,
            schema: $schema,
            categories: $categories,
        );
    }
}
