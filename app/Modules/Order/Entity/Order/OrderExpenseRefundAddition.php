<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $refund_id
 * @property int $expense_addition_id
 * @property float $amount
 * @property OrderExpenseAddition $expenseAddition
 * @property OrderExpenseRefund $refund
 */
class OrderExpenseRefundAddition extends Model
{
    public $timestamps = false;
    protected $table = 'order_expense_refund_additions';
    protected $fillable = [
        'expense_addition_id',
        'amount',
    ];
    protected $casts = [
        'amount' => 'float',
    ];

    public static function new(int $expense_addition_id, float $amount): self
    {
        return self::make([
            'expense_addition_id' => $expense_addition_id,
            'amount' => $amount,
        ]);
    }

    public function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }

    public function expenseAddition(): BelongsTo
    {
        return $this->belongsTo(OrderExpenseAddition::class, 'expense_addition_id', 'id');
    }

    public function refund(): BelongsTo
    {
        return $this->belongsTo(OrderExpenseRefund::class, 'refund_id', 'id');
    }
}
