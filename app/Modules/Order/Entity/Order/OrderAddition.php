<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;
use function now;

/**
 * Дополнительные оплаты(услуги) к заказу
 * @property int $id
 * @property int $order_id
 * @property float $amount
// * @property Carbon $created_at
 * @property int $purpose
 * @property string $comment
 * @property Order $order
 * @property OrderExpenseAddition[] $expenseAdditions
 */

class OrderAddition extends Model
{
    /**
     * Назначения платежа $purpose
     */
  //  const PAY_ORDER = 101;
    const PAY_DELIVERY = 102;
    const PAY_PACKING = 103;
    const PAY_ASSEMBLY = 104;
    const PAY_LIFTING = 105;
    const PAY_OTHER = 109;

    const PAYS = [
      //  self::PAY_ORDER => 'Платеж за заказ',
        self::PAY_DELIVERY => 'Платеж за доставку',
        self::PAY_PACKING => 'Платеж за упаковку',
        self::PAY_LIFTING => 'Платеж за подъем',
        self::PAY_ASSEMBLY => 'Платеж за сборку',
        self::PAY_OTHER => 'Другие платежи',
    ];

    protected $table = 'order_additions';

    public $timestamps = false;
    protected $fillable = [
        'amount',
        'purpose',
       // 'created_at',
        'comment',
    ];
/*
    protected $casts = [
        'created_at' => 'datetime',
    ];

*/
    public static function new(float $amount, int $purpose, string $comment = ''): self
    {
        return self::make([
            'amount' => $amount,
            'comment' => $comment,
            'purpose' => $purpose,
            //'created_at' => now(),
        ]);
    }

    /**
     * Установка или смена назначения платежа
     * @param int $purpose
     * @param string $comment
     * @return void
     */
    public function setType(int $purpose, string $comment = ''): void
    {
        $this->purpose = $purpose;
        $this->comment = $comment;
        $this->save();
    }


    public function purposeHTML(): string
    {
        return self::PAYS[$this->purpose];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function expenseAdditions()
    {
        return $this->hasMany(OrderExpenseAddition::class, 'order_addition_id', 'id');
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
}
