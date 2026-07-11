<?php

namespace App\Modules\Shop\Application\Queries\Room;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Application\DTOs\CategoryRoomIndexPageData;
use App\Modules\Shop\Application\DTOs\Parts\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\RoomTreeQueryRepository;
use Illuminate\Support\Facades\Cache;

class RoomIndexQuery
{
    public function __construct(
        private RoomTreeQueryRepository $treeRepo,
        private Settings                    $settings,
    )
    {
    }

    public function execute(): CategoryRoomIndexPageData
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

        return new CategoryRoomIndexPageData(
            meta: new SeoData(
                title: $web->rooms_title,
                description: $web->rooms_desc,
            ),
            categories: $categories,
        );
    }
}
