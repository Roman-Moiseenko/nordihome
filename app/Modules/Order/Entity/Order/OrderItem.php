<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Discount\Entity\Discount;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\CartItemInterface;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;


/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property bool $preorder //на предзаказ
 * @property int $supplier_document_id //Заказ поставщику
 * @property int $base_cost
 * @property int $sell_cost
 * @property int $discount_id - Акция или бонус
 * @property string $discount_type
 * @property array $options
 * @property bool $cancel
 * @property string $comment
 * @property int $reserve_id
 * @property bool $assemblage - требуется сборка
 * @property Order $order
 * @property Reserve $reserve - в резерве
 * @property Product $product
 * @property Discount $discount
 * @property OrderExpenseItem[] $expenseItems
 */
class OrderItem extends Model implements CartItemInterface
{
    public $timestamps = false;
    protected $fillable = [
        'quantity',
        'product_id',
        'base_cost',
        'sell_cost',
        'discount_id',
        'options',
        'cancel',
        'comment',
        'reserve_id',
        'discount_type',
        'preorder',
        'assemblage',
    ];

    protected $casts = [
        'options' => 'json',
        'base_cost' => 'float',
        'sell_cost' => 'float',
        'preorder' => 'bool'
    ];
/*
    public function getAssemblage(): float
    {
        if ()
    } */

    public function changeQuantity(int $new_quantity)
    {
        $this->quantity = $new_quantity;
        $this->save();
    }

    public function clearReserve()
    {
        $this->update(['reserve_id' => null]);
    }

    public function expenseItems()
    {
        return $this->hasMany(OrderExpenseItem::class, 'order_item_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function reserve()
    {
        return $this->belongsTo(Reserve::class, 'reserve_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }

    public function discountName()
    {
        if (empty($this->discount_id)) return '';
        $discount = $this->discount_type::find($this->discount_id);
        return $this->discount_type::TYPE . ' ' . $discount->title;
    }

    /**
     * Сколько выдано данного товара
     * @return int
     */
    public function getExpenseAmount(): int
    {
        $result = 0;
        foreach ($this->expenseItems as $expenseItem) {
            $result += $expenseItem->quantity;
        }
        return $result;
    }

    /**
     * Не выданный остаток
     * @return int
     */
    #[Pure] public function getRemains(): int
    {
        return $this->quantity - $this->getExpenseAmount();
    }

    public function getAssemblage(int $percent = 15): float
    {
        if ($this->assemblage == true) {
            if (is_null($this->product->assemblage)) {
                return $this->sell_cost * $this->quantity * $percent / 100;
            } else {
                return $this->product->assemblage;
            }
        }
        return 0;
    }

    /**
     * Функция для контроля кол-ва
     * @return bool
     */
    #[Pure] public function check_reserve(): bool
    {
        $reserve_q = $this->reserve->quantity;
        $expense_q = $this->getExpenseAmount();
        return $this->quantity == ($reserve_q + $expense_q); //Кол-во равно в резерве+выдано
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getBaseCost(): float
    {
        return $this->base_cost;
    }

    public function getSellCost(): float
    {
        return $this->sell_cost;
    }

    public function getDiscount(): ?int
    {
        return $this->discount_id;
    }

    public function getDiscountType(): string
    {
        return $this->discount_type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getReserve(): ?Reserve
    {
        return $this->reserve;
    }

    public function getCheck(): bool
    {
        return true;
    }

    public function setSellCost(float $discount_cost): void
    {
        $this->sell_cost = $discount_cost;
        $this->save();
    }

    public function setDiscountName(string $discount_name): void
    {
        //
    }

    public function setDiscount(int $discount_id): void
    {
        $this->discount_id = $discount_id;
        $this->save();
    }

    public function setDiscountType(string $discount_type): void
    {
        $this->discount_type = $discount_type;
        $this->save();
    }

    public function getPreorder(): bool
    {
        return $this->preorder;
    }
}
