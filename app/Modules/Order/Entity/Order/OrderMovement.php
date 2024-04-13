<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Accounting\Entity\MovementDocument;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $order_id
 * @property int $movement_id
 * @property Order $order
 * @property MovementDocument $movement
 */
class OrderMovement extends Model
{
    public $timestamps = false;
    protected $table = 'orders_movements';

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function movement()
    {
        return $this->belongsTo(MovementDocument::class, 'movement_id', 'id');
    }
}
