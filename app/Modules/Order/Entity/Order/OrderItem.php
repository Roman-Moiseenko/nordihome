<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property int $base_cost
 * @property int $sell_cost
 * @property int $discount_id
 * @property array $options
 * @property bool $cancel
 * @property string $comment
 * @property int $reserve_id
 */
class OrderItem extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'quantity',
        'product_id',
        'base_cost',
        'sell_cost',
        'discount_id',
        'options',
        'cancel',
        'comment',
        'reserve_id',
    ];

    protected $casts = [
        'options' => 'json',
        'base_cost' => 'float',
        'sell_cost' => 'float',
    ];

    public function clearReserve()
    {
        $this->update(['reserve_id' => null]);
    }
}
