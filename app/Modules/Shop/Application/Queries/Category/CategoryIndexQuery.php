<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries\Category;

use App\Modules\Setting\Application\Actions\GetWebSettingsUseCase;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\CatalogIndexPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryTreeQueryRepository;
use App\Modules\Storefront\Infrastructure\Persistence\CacheInvalidationRegistry;
use Illuminate\Support\Facades\Cache;

readonly class CategoryIndexQuery
{
    public function __construct(
        private CategoryTreeQueryRepository $treeRepo,
        private GetWebSettingsUseCase $webSettingsUseCase,
        private SchemaBuilder               $schemaBuilder,
    )
    {
    }

    public function execute(): CatalogIndexPageData
    {
        $web = $this->webSettingsUseCase->execute();

        $categories = Cache::remember(
            CacheInvalidationRegistry::CATEGORY_INDEX_PAGE,
            now()->addDay(),
            fn() => array_map(
                fn($item) => new CategoryRoomData(
                    id: $item->id,
                    name: $item->name,
                    slug: $item->slug,
                    image: $item->image,
                ),
                $this->treeRepo->getChildren(),
            ),
        );

        $meta = new SeoData(
            title: $web->categories_title,
            description: $web->categories_desc,
            canonical: route('shop.category.index'),
            ogSiteName: $web->web_name,
        );

        $schema = $this->schemaBuilder->buildForCategoryIndex($categories, 'category');
        return new CatalogIndexPageData(
            meta: $meta,
            categories: $categories,
            schema: $schema,
        );
    }
}
