<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Guide\Entity\Addition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;
use function now;

/**
 * Дополнительные оплаты(услуги) к заказу
 * @property int $id
 * @property int $order_id
 * @property int $amount
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $addition_id
 * @property int $quantity
 * @property Order $order
 * @property Addition $addition
 * @property OrderExpenseAddition[] $expenseAdditions
 */

class OrderAddition extends Model
{

    protected $table = 'order_additions';
    protected $touches = [
        'order',
    ];
    protected $fillable = [
        'addition_id',
        'comment',
        'quantity',
    ];

    public static function new(int $addition_id): self
    {
        return self::make([
            'comment' => '',
            'addition_id' => $addition_id,
            'quantity' => 1,
        ]);
    }

    public function getAmount(): float|int
    {
        if ($this->addition->manual) {
            $amount = $this->amount ?? 0; //Цена ставится в ручную после добавления в заказ
        } else {
            if (is_null($this->addition->class)) {
                $amount = $this->addition->base; //Цена фиксирована,из справочника
            } else {
                //Цена рассчитывается по своему алгоритму
                $amount = $this->addition->class::calculate($this->order, $this->addition->base);
            }
        }
        return $amount * $this->quantity;
    }


    #[Pure] public function getRemains(): float
    {
        return $this->amount - $this->getExpenseAmount();
    }

    public function getExpenseAmount(): float
    {
        $result = 0;
        foreach ($this->expenseAdditions as $expenseAddition) {
            $result += $expenseAddition->amount;
        }
        return $result;
    }

    //RELATIONS
    public function addition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Addition::class, 'addition_id', 'id');
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function expenseAdditions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderExpenseAddition::class, 'order_addition_id', 'id');
    }

}
