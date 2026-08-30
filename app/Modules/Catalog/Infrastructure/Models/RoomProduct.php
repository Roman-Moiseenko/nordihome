<?php

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $room_id
 * @property int $product_id
 * @property Room $room
 */
class RoomProduct extends Model
{
    public $timestamps = false;
    protected $touches = ['room'];

    protected $table = 'rooms_products';

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
