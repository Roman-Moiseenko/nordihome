<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $refund_id
 * @property int $order_item_id
 * @property float $quantity
 * @property OrderItem $orderItem
 */
class OrderRefundItem extends Model
{
    public $timestamps = false;
    protected $table = 'order_refund_items';
    protected $fillable = [
        'order_item_id',
        'quantity',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }
}
