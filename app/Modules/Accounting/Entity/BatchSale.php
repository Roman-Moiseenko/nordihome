<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;


use App\Modules\Order\Entity\Order\OrderExpenseItem;
use App\Modules\Order\Entity\Order\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Продажи по партиям
 * @property int $id - ??
 * @property float $quantity
 * @property float $cost - Себестоимость
 * @property int $arrival_product_id
 * @property int $surplus_product_id
 * @property int $expense_item_id
 *
 * Для ускоренной работы
 * @property int $product_id - для Статистики по товару
 * @property int $sell_cost
 *
 * @property ArrivalProduct $arrivalProduct
 * @property SurplusProduct $surplusProduct
 * @property OrderExpenseItem $expenseItem
 */

class BatchSale extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'expense_item_id',
        'quantity',
        'cost',
        'arrival_product_id',
        'surplus_product_id',
    ];
    protected $casts = [
        'quantity' => 'float',
        'cost' => 'float',
    ];

    public static function register(int $expense_item_id, float $quantity, float $cost,
                                    ?int $arrival_product_id, ?int $surplus_product_id): self
    {
        return self::create([
            'expense_item_id' => $expense_item_id,
            'quantity' => $quantity,
            'cost' => $cost,
            'arrival_product_id' => $arrival_product_id,
            'surplus_product_id' => $surplus_product_id,
        ]);
    }

    public function arrivalProduct(): BelongsTo
    {
        return $this->belongsTo(ArrivalProduct::class, 'arrival_product_id', 'id');
    }

    public function surplusProduct(): BelongsTo
    {
        return $this->belongsTo(SurplusProduct::class, 'surplus_product_id', 'id');
    }

    public function expenseItem(): BelongsTo
    {
        return $this->belongsTo(OrderExpenseItem::class, 'expense_item_id', 'id');
    }
}
