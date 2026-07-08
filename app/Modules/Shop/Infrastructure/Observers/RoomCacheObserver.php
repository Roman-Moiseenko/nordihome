<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Catalog\Infrastructure\Models\Room;
use Illuminate\Support\Facades\Cache;

class RoomCacheObserver
{
    public function saved(Room $room): void
    {
        Cache::forget('client_room_tree');
    }

    public function deleted(Room $room): void
    {
        Cache::forget('client_room_tree');
    }
}
