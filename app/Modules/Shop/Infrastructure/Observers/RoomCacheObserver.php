<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use Illuminate\Support\Facades\Cache;

class RoomCacheObserver
{
    public function __construct(
        private readonly CacheInvalidationRegistry $registry
    ) {}

    public function saved(Room $room): void
    {
        $this->registry->forgetRoom($room->id);

    }

    public function deleted(Room $room): void
    {
        $this->registry->forgetRoom($room->id);
    }
}
