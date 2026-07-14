<?php

namespace App\Modules\Shop\Application\Queries\Room;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\CatalogIndexPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\RoomTreeQueryRepository;
use Illuminate\Support\Facades\Cache;

readonly class RoomIndexQuery
{
    public function __construct(
        private RoomTreeQueryRepository $treeRepo,
        private Settings                $settings,
        private SchemaBuilder           $schemaBuilder,
    )
    {
    }

    public function execute(): CatalogIndexPageData
    {
        $web = $this->settings->web;

        $categories = Cache::remember(
            CacheInvalidationRegistry::ROOM_INDEX_PAGE,
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

        $schema = $this->schemaBuilder->buildForCategoryIndex($categories, 'room');
        return new CatalogIndexPageData(
            meta: new SeoData(
                title: $web->rooms_title,
                description: $web->rooms_desc,
            ),
            categories: $categories,
            schema: $schema,
        );
    }
}
