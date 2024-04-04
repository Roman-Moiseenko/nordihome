<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $expense_id
 * @property int $order_addition_id
 * @property float $amount
 * @property OrderAddition $orderAddition
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

    public static function new(int $order_addition_id, float $amount): self
    {
        return self::make([
            'order_addition_id' => $order_addition_id,
            'amount' => $amount,
        ]);
    }

    public function orderAddition()
    {
        return $this->belongsTo(OrderAddition::class, 'order_addition_id', 'id');
    }
}
