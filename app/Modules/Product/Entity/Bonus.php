<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $product_id
 * @property int $bonus_id
 * @property int $discount
 */
class Bonus extends Model
{
    protected $table = 'bonus_products';
    public $timestamps = false;
    protected $fillable = [
        'discount',
    ];
}
