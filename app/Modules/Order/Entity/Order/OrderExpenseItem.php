<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $expense_id
 * @property int $order_item_id
 * @property float $quantity
 * @property OrderItem $orderItem
 * @property OrderExpense $expense
 * @property OrderExpenseRefundItem[] $refundItems
 * @property array $honest_signs
 */
class OrderExpenseItem extends Model
{
    public $timestamps = false;
    protected $table = 'order_expense_items';
    protected $appends = [
        'honest_signs' => '{}',
    ];
    public $fillable = [
        'expense_id',
        'order_item_id',
        'quantity'
    ];

    public function casts(): array
    {
        return [
            'quantity' => 'float',
            'honest' => 'array'
        ];
    }
    public static function new(int $order_item_id, float $quantity): self
    {
        return self::make([
            'order_item_id' => $order_item_id,
            'quantity' => $quantity,
        ]);
    }

    public function orderItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }

    public function expense(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderExpense::class, 'expense_id', 'id');
    }

    public function refundItems(): HasMany
    {
        return $this->hasMany(OrderExpenseRefundItem::class, 'expense_item_id', 'id');
    }

    public function quantityNotRefund(): float
    {
        return $this->quantity - $this->quantityRefund();
    }

    public function quantityRefund(): float
    {
        $quantity = 0;
        foreach ($this->refundItems as $item) {
            if ($item->refund->isCompleted()) $quantity += $item->quantity;
        }
        return $quantity;
    }
}
