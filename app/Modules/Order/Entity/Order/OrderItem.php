<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Discount\Entity\Discount;
use App\Modules\Order\Entity\OrderReserve;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\CartItemInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;


/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property float $quantity
 * @property bool $preorder //на предзаказ
 * @property int $supply_stack_id - данная позиция в стеке заказов
 * @property int $base_cost - базовая цена, не меняется
 * @property int $sell_cost - цена продажи, со скидкой, можно ставить вручную
 * @property int $discount_id - Акция или бонус
 * @property string $discount_type
 * @property bool $fix_manual - фиксированная цена со скидкой, блокирует автоматический пересчет
 * @property array $options
 * @property bool $cancel -- ?
 * @property string $comment
 * @property int $reserve_id
 * @property bool $assemblage - требуется сборка
 * @property bool $packing - требуется упаковка
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Order $order
 * @property Product $product
 * @property Discount $discount
 * @property OrderExpenseItem[] $expenseItems
 * @property SupplyStack $supplyStack
 * @property OrderReserve[] $reserves
 */
class OrderItem extends Model implements CartItemInterface
{
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
        'supply_stack_id',
        'fix_manual',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'options' => 'json',
        'base_cost' => 'float',
        'sell_cost' => 'float',
        'preorder' => 'bool',
        'fix_manual' => 'bool',
        'quantity' => 'float',
    ];
    protected $touches = [
        'order',
        ];

    public static function new(Product $product, float $quantity, bool $preorder/*, int $user_id*/): self
    {
        return self::make([
            'quantity' => $quantity,
            'product_id' => $product->id,
            //'base_cost' => $product->getLastPrice($user_id),
            //'sell_cost' => $product->getLastPrice($user_id),
            'options' => [],
            'preorder' => $preorder,
        ]);
    }

    public function changeQuantity(float $new_quantity)
    {
        $this->quantity = $new_quantity;
        $this->save();
    }


    //*** GET-...

    public function getPercent():float
    {
        if ($this->base_cost == 0) return 0;
        return ceil(($this->base_cost - $this->sell_cost) / $this->base_cost * 100);
    }

    public function getReserveByStorageItem(int $storage_item_id):? OrderReserve
    {
        foreach ($this->reserves as $reserve) {
            if ($reserve->storage_item_id == $storage_item_id) return $reserve;
        }
        return null;
    }

    public function getReserveByStorage(int $storage_id):? OrderReserve
    {
        foreach ($this->reserves as $reserve) {
            if ($reserve->storageItem->storage_id == $storage_id) return $reserve;
        }
        return null;
    }


    /**
     * Сколько выдано данного товара
     * @return float
     */
    public function getExpenseAmount(): float
    {
        $result = 0;
        foreach ($this->expenseItems as $expenseItem) {
            $result += $expenseItem->quantity;
        }
        return $result;
    }

    /**
     * Не выданный остаток
     * @return float
     */
    #[Pure] public function getRemains(): float
    {
        return $this->quantity - $this->getExpenseAmount();
    }

    public function getAssemblage(int $percent = 15): float
    {
        if ($this->assemblage) {
            if (is_null($this->product->assemblage)) {
                return (int)ceil($this->sell_cost * $this->quantity * $percent / 100);
            } else {
                return $this->product->assemblage;
            }
        }
        return 0;
    }


    //RELATIONS
    public function reserves(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderReserve::class, 'order_item_id', 'id');
    }

    public function supplyStack(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        if (!$this->preorder) throw new \DomainException('Данная функция должна вызываться для preorder == true');
        return $this->belongsTo(SupplyStack::class, 'supply_stack_id', 'id');
    }

    public function expenseItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderExpenseItem::class, 'order_item_id', 'id');
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
/*
    public function reserve()
    {
        return $this->belongsTo(Reserve::class, 'reserve_id', 'id');
    }
*/
    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function discount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }


    public function clearReserves(): void
    {
        foreach ($this->reserves as $reserve) {
            $reserve->delete();
        }
    }

    public function updateReserves(Carbon $addDays): void
    {
        foreach ($this->reserves as $reserve) {
            $reserve->update(['reserve_at' => $addDays]);
        }
    }

    public function discountName(): string
    {
        if (empty($this->discount_id)) return '';
        $discount = $this->discount_type::find($this->discount_id);
        return $this->discount_type::TYPE . ' ' . $discount->title;
    }

    /**
     * Функция для контроля кол-ва
     * @return bool
     */
/*    #[Pure] public function check_reserve(): bool
    {
        $reserve_q = $this->reserve->quantity;
        $expense_q = $this->getExpenseAmount();
        return $this->quantity == ($reserve_q + $expense_q); //Кол-во равно в резерве+выдано
    }
*/
    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): float
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

    /**
     * Функция НЕ сохраняет данные в базу
     * @param float|null $base
     * @param float|null $sell
     * @return void
     */
    public function setCost(float|null $base, float|null $sell): void
    {
        if (!is_null($base)) $this->base_cost = $base;
        if (!is_null($sell)) $this->sell_cost = $sell;
    }


}
