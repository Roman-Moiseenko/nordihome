<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Catalog\Infrastructure\Models\Group;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;

class GroupProductCacheObserver
{
    public function __construct(
        private CacheInvalidationRegistry $registry
    ) {}
    public function saved(Group $group): void
    {
        $this->registry->forgetGroup($group->id);
    }

    public function deleted(Group $group): void
    {
        $this->registry->forgetGroup($group->id);
    }
}
