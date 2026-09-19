<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Storefront\Infrastructure\Persistence\CacheInvalidationRegistry;

class IkeaCategoryCacheObserver
{
    public function __construct(
        private CacheInvalidationRegistry $registry
    ) {}
    public function saved(ParserCategory $category): void
    {
        $this->registry->forgetIkeaCategory($category->id);
    }

    public function deleted(ParserCategory $category): void
    {
        $this->registry->forgetIkeaCategory($category->id);

    }
}
