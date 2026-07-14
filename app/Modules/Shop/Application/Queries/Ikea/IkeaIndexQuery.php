<?php

namespace App\Modules\Shop\Application\Queries\Ikea;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\IkeaIndexPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\IkeaTreeQueryRepository;
use Illuminate\Support\Facades\Cache;

readonly class IkeaIndexQuery
{
    public function __construct(
        private IkeaTreeQueryRepository $treeRepo,
        private Settings      $settings,
        private SchemaBuilder $schemaBuilder,
    )
    {
    }

    public function execute(): IkeaIndexPageData
    {
        $web = $this->settings->web;

        $categories = Cache::remember(
            CacheInvalidationRegistry::IKEA_CATEGORY_INDEX_PAGE,
            now()->addDay(),
            fn() => $this->treeRepo->getFullTree(),
        );

        $schema = $this->schemaBuilder->buildForCategoryIndex($categories, 'ikea');

        return new IkeaIndexPageData(
            meta: new SeoData(
                title: $web->ikea_title,
                description: $web->ikea_desc,
            ),
            schema: $schema,
            categories: $categories,
        );
    }
}
