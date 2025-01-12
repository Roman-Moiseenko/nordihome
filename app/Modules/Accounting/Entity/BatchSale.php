<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;


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
 * @property int $order_item_id
 *
 * Для ускоренной работы
 * @property int $product_id - для Статистики по товару
 * @property int $sell_cost
 * @property int $expense_id
 *
 * @property ArrivalProduct $arrivalProduct
 * @property OrderItem $orderItem
 * @property SurplusProduct $surplusProduct
 */

class BatchSale extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'arrival_product_id',
        'order_item_id',
        'quantity',
        'cost',
        'surplus_product_id',
    ];

    public static function register(?int $arrival_product_id, int $order_item_id, float $quantity, float $cost, ?int $surplus_product_id): self
    {
        return self::create([
            'arrival_product_id' => $arrival_product_id,
            'order_item_id' => $order_item_id,
            'quantity' => $quantity,
            'cost' => $cost,
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

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }
}
