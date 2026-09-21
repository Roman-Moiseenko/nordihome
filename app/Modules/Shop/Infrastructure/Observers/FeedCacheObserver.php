<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Output\Infrastructure\Models\Feed;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;

class FeedCacheObserver
{
    public function __construct(
        private readonly CacheInvalidationRegistry $registry,
    ) {}

    public function saved(Feed $feed): void
    {
        $this->registry->forgetFeeds();
    }

    public function deleted(Feed $feed): void
    {
        $this->registry->forgetFeeds();
    }
}
