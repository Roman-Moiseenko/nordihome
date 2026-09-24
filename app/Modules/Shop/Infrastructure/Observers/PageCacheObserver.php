<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Content\Infrastructure\Models\Page;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;

class PageCacheObserver
{
    public function __construct(
        private readonly CacheInvalidationRegistry $registry
    ) {}

    public function saved(Page $page): void
    {
        $this->registry->forgetPage($page->slug);
    }

    public function deleted(Page $page): void
    {
        $this->registry->forgetPage($page->slug);
    }
}
