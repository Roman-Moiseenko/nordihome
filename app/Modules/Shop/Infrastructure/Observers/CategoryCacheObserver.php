<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Storefront\Infrastructure\Persistence\CacheInvalidationRegistry;

class CategoryCacheObserver
{
    public function __construct(
        private CacheInvalidationRegistry $registry
    ) {}
    public function saved(Category $category): void
    {
        $this->registry->forgetCategory($category->id);
    }

    public function deleted(Category $category): void
    {
        $this->registry->forgetCategory($category->id);
    }
}
