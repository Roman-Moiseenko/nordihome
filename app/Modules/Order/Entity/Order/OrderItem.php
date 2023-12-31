<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Order\Entity\Reserve;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property int $base_cost
 * @property int $sell_cost
 * @property int $discount_id
 * @property string $discount_type
 * @property array $options
 * @property bool $cancel
 * @property string $comment
 * @property int $reserve_id
 * @property Order $order
 * @property Reserve $reserve
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
        'discount_type'
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

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function reserve()
    {
        return $this->belongsTo(Reserve::class, 'reserve_id', 'id');
    }
}
