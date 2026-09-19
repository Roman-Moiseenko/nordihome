<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries\Menu;

use App\Modules\Shop\Application\DTOs\Menu\MenuData;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\MenuQueryRepository;
use Illuminate\Support\Facades\Cache;

readonly class GetMenusQuery
{
    public function __construct(
        private MenuQueryRepository $repository
    )
    {
    }

    /**
     * @return array<string, MenuData>
     */
    public function execute(): array
    {
        return Cache::remember(
            CacheInvalidationRegistry::MENUS,
            now()->addDay(),
            function (): array {
                $menus = $this->repository->getMenusWithItems();
                $indexed = [];
                foreach ($menus as $menu) {
                    $indexed[$menu->slug] = $menu;
                }
                return $indexed;
            }
        );
    }
}
