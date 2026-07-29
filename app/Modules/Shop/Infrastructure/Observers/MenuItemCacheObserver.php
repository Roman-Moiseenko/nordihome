<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Content\Entity\MenuItem;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;

class MenuItemCacheObserver
{
    public function __construct(
        private CacheInvalidationRegistry $registry
    ) {}

    public function saved(MenuItem $item): void
    {
        $this->registry->forgetMenus();
    }

    public function deleted(MenuItem $item): void
    {
        $this->registry->forgetMenus();
    }
}
