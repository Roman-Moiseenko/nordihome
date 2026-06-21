<?php

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;
/**
 * @property int $product_id
 * @property int $room_id
 */
class ProductRoom extends Model
{
    public $timestamps = false;
    protected $table = 'products_rooms';
}
