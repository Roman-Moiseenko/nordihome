<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $product_id
 * @property int $quantity
 * @property int $discount
 */
class BonusQuantity extends Model
{
    protected $table = 'bonus_quantity';
    public $timestamps = false;
    protected $fillable = [
        'quantity',
        'discount',
    ];
}
