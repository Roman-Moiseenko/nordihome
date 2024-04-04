<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $expense_id
 * @property int $order_item_id
 * @property int $quantity
 * @property OrderItem $orderItem
 */
class OrderExpenseItem extends Model
{
    public $timestamps = false;
    protected $table = 'order_expense_items';
    public $fillable = [
        'expense_id',
        'order_item_id',
        'quantity'
    ];

    public static function new(int $order_item_id, int $quantity): self
    {
        return self::make([
            'order_item_id' => $order_item_id,
            'quantity' => $quantity,
        ]);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }
}
