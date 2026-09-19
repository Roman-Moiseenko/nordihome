<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Content\Entity\Menu;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;

class MenuCacheObserver
{
    public function __construct(
        private CacheInvalidationRegistry $registry
    ) {}

    public function saved(Menu $menu): void
    {
        $this->registry->forgetMenus();
    }

    public function deleted(Menu $menu): void
    {
        $this->registry->forgetMenus();
    }
}
