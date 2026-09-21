<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Content\Entity\Page;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;

class PageCacheObserver
{
    public function __construct(
        private readonly CacheInvalidationRegistry $registry
    ) {}

    public function saved(Page $page): void
    {
        $this->registry->forgetSitemap();
    }

    public function deleted(Page $page): void
    {
        $this->registry->forgetSitemap();
    }
}
