<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;

class PostCacheObserver
{
    public function __construct(
        private readonly CacheInvalidationRegistry $registry
    ) {}

    public function saved(Post $post): void
    {
        $this->registry->forgetSitemap();
    }

    public function deleted(Post $post): void
    {
        $this->registry->forgetSitemap();
    }
}
