<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $expense_id
 * @property int $order_addition_id
 * @property float $amount
 * @property OrderAddition $orderAddition
 * @property OrderExpenseRefundAddition[] $refundAdditions
 */
class OrderExpenseAddition extends Model
{
    public $timestamps = false;
    protected $table = 'order_expense_additions';
    public $fillable = [
        'expense_id',
        'order_addition_id',
        'amount'
    ];
    public function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }

    public static function new(int $order_addition_id, float $amount): self
    {
        return self::make([
            'order_addition_id' => $order_addition_id,
            'amount' => $amount,
        ]);
    }

    public function orderAddition(): BelongsTo
    {
        return $this->belongsTo(OrderAddition::class, 'order_addition_id', 'id');
    }

    public function refundAdditions(): HasMany
    {
        return $this->hasMany(OrderExpenseRefundAddition::class, 'expense_addition_id', 'id');
    }

    public function amountNotRefund(): float
    {
        return $this->amount - $this->amountRefund();
    }

    public function amountRefund(): float
    {
        $amount = 0;
        foreach ($this->refundAdditions as $item) {
            if ($item->refund->isCompleted()) $amount += $item->amount;
        }
        return $amount;
    }

}
