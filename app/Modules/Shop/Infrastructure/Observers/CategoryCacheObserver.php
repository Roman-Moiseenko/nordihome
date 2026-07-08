<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Catalog\Infrastructure\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryCacheObserver
{
    public function saved(Category $category): void
    {
        Cache::forget('client_category_tree');
    }

    public function deleted(Category $category): void
    {
        Cache::forget('client_category_tree');
    }
}
