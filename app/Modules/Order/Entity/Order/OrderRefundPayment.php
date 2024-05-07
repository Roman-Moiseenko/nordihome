<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $refund_id
 * @property int $order_payment_id
 * @property float $amount
 * @property OrderPayment $orderPayment
 */
class OrderRefundPayment extends Model
{
    public $timestamps = false;
    protected $table = 'order_refund_payments';
    protected $fillable = [
        'order_payment_id',
        'amount',
    ];

    public function orderPayment()
    {
        return $this->belongsTo(OrderPayment::class, 'order_payment_id', 'id');
    }
}
