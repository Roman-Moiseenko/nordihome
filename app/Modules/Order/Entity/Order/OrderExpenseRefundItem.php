<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $refund_id
 * @property int $expense_item_id
 * @property float $quantity
 * @property OrderExpenseItem $expenseItem
 * @property OrderExpenseRefund $refund
 */
class OrderExpenseRefundItem extends Model
{
    public $timestamps = false;
    protected $table = 'order_expense_refund_items';
    protected $fillable = [
        'expense_item_id',
        'quantity',
    ];

    public function casts(): array
    {
        return ['quantity' => 'float'];
    }

    public static function new(int $expense_item_id, float $quantity): self
    {
        return self::make([
            'expense_item_id' => $expense_item_id,
            'quantity' => $quantity,
        ]);
    }

    public function expenseItem(): BelongsTo
    {
        return $this->belongsTo(OrderExpenseItem::class, 'expense_item_id', 'id');
    }

    public function refund(): BelongsTo
    {
        return $this->belongsTo(OrderExpenseRefund::class, 'refund_id', 'id');
    }
}
