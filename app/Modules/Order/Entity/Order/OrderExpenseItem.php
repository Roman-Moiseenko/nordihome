<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $expense_id
 * @property int $order_item_id
 * @property float $quantity
 * @property OrderItem $orderItem
 * @property OrderExpense $expense
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

    public static function new(int $order_item_id, float $quantity): self
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

    public function expense()
    {
        return $this->belongsTo(OrderExpense::class, 'expense_id', 'id');
    }
}
