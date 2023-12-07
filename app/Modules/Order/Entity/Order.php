<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property bool $paid
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $amount
 * @property int $discount
 * @property int $coupon
 * @property int $coupon_id
 *
 */
class Order extends Model
{
    const STATUS_DRAFT = 201;
    const STATUS_FORMED = 201;
    const STATUS_AWAITING = 201;
    const STATUS_PAID = 201;
    const STATUS_DELIVERED = 201;
    const STATUS_COMPLETED = 201;
    const STATUS_CANCEL = 201;


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }



}
