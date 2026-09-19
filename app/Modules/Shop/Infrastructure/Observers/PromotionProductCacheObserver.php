<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Storefront\Infrastructure\Persistence\CacheInvalidationRegistry;

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
