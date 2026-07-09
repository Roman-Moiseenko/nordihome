<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use Illuminate\Support\Facades\Cache;

class IkeaCategoryCacheObserver
{
    public function saved(ParserCategory $category): void
    {
        Cache::forget('client_ikea_tree');
    }

    public function deleted(ParserCategory $category): void
    {
        Cache::forget('client_ikea_tree');
    }
}
