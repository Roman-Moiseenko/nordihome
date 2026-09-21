<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;

class ProductCacheObserver
{
    public function __construct(
        private readonly CacheInvalidationRegistry $registry
    ) {}

    public function saved(Product $product): void
    {
        $this->registry->forgetSitemap();
    }

    public function deleted(Product $product): void
    {
        $this->registry->forgetSitemap();
    }
}
