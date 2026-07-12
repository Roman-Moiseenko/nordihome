<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries\Category;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\CatalogIndexPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryTreeQueryRepository;
use Illuminate\Support\Facades\Cache;

readonly class CategoryIndexQuery
{
    public function __construct(
        private CategoryTreeQueryRepository $treeRepo,
        private Settings                    $settings,
        private SchemaBuilder               $schemaBuilder,
    )
    {
    }

    public function execute(): CatalogIndexPageData
    {
        $web = $this->settings->web;

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
        //FIXME
        $schema = $this->schemaBuilder->createSchema();
        return new CatalogIndexPageData(
            meta: new SeoData(
                title: $web->categories_title,
                description: $web->categories_desc,
            ),
            categories: $categories,
            schema: $schema,
        );
    }
}
