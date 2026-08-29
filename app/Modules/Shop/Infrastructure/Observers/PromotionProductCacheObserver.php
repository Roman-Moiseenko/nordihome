<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Discount\Infrastructure\Models\PromotionProduct;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use Illuminate\Support\Facades\Cache;

class PromotionProductCacheObserver
{
    public function __construct(
        private CacheInvalidationRegistry $registry
    ) {}
    public function saved(Promotion $promotion): void
    {
        $this->registry->forgetPromotion($promotion->id);
    }

    public function deleted(Promotion $promotion): void
    {
        $this->registry->forgetPromotion($promotion->id);
    }
}
