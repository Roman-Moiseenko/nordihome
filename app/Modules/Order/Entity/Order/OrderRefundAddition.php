<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $refund_id
 * @property int $order_addition_id
 * @property float $amount
 * @property OrderAddition $orderAddition
 */
class OrderRefundAddition extends Model
{
    public $timestamps = false;
    protected $table = 'order_refund_additions';
    protected $fillable = [
        'order_addition_id',
        'amount',
    ];

    public function orderAddition()
    {
        return $this->belongsTo(OrderAddition::class, 'order_addition_id', 'id');
    }
}
