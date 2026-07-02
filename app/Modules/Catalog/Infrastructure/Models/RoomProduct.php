<?php

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $room_id
 * @property int $product_id
 */
class RoomProduct extends Model
{
    public $timestamps = false;
    protected $table = 'rooms_products';

}
