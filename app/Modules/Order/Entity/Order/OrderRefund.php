<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Admin\Entity\Admin;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $staff_id
 * @property string $number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $comment
 * @property float $amount - сумма на возврат
 * @property OrderRefundItem[] $items
 * @property OrderRefundAddition[] $additions
 * @property OrderRefundPayment[] $payments
 * @property Order $order
 * @property Admin $staff
 */
class OrderRefund extends Model
{
    use HtmlInfoData;

 /*   const NEW = 80;
    const CONFIRMED = 81; //Проставляет ответственный за деньги, руководитель
    const PAID = 82; //Проставляет Бухгалтер
    const COMPLETED = 89; //Проставляет менеджер, отчитался за работу!

    const STATUSES = [
        self::NEW => 'Новый',
        self::CONFIRMED => 'Подтвержденный',
        self::PAID => 'Оплачено',
        self::COMPLETED => 'Завершен',
    ];
*/
    protected $table = 'order_refunds';
    protected $fillable = [
        'order_id',
        'staff_id',
        'created_at',
        'updated_at',
        'comment',
        'amount',
        'number'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $order_id, int $staff_id, string $comment, string $number): self
    {
        return self::create([
            'order_id' => $order_id,
            'staff_id' => $staff_id,
            'comment' => $comment,
            'number' => $number
        ]);
    }
/*
    public function getBalanceOrder(): float
    {
        return $this->order->getPaymentAmount() - $this->order->getExpenseAmount();
    }

    public function getRefundAmount(): float
    {
        return min($this->getBalanceOrder(), $this->getAmount());
    }

    public function getAmount(): float
    {
        $amount = 0;
        foreach ($this->items as $item) {
            $amount += $item->quantity * $item->orderItem->sell_cost;
        }

        foreach ($this->additions as $addition) {
            $amount += $addition->amount;
        }
        return $amount;
    }
*/
    public function  getQuantity(): float
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->quantity;
        }
        return $quantity;
    }

    public function items()
    {
        return $this->hasMany(OrderRefundItem::class, 'refund_id', 'id');
    }

    public function additions()
    {
        return $this->hasMany(OrderRefundAddition::class, 'refund_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(OrderRefundPayment::class, 'refund_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    ///*** Хелперы

}
