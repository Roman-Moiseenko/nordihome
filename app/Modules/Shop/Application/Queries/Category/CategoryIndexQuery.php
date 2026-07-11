<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries\Category;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Application\DTOs\CategoryRoomIndexPageData;
use App\Modules\Shop\Application\DTOs\Parts\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryTreeQueryRepository;
use Illuminate\Support\Facades\Cache;

readonly class CategoryIndexQuery
{
    public function __construct(
        private CategoryTreeQueryRepository $treeRepo,
        private Settings                    $settings,
    )
    {
    }

    public function execute(): CategoryRoomIndexPageData
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

        return new CategoryRoomIndexPageData(
            meta: new SeoData(
                title: $web->categories_title,
                description: $web->categories_desc,
            ),
            categories: $categories,
        );
    }
}
