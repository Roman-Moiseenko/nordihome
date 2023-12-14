<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Order\Entity\Payment\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $type //ONLINE, MANUAL, SHOP
 * @property bool $preorder
 * @property bool $paid //Оплачен (для быстрой фильтрации)
 * @property bool $finished //Завершен (для быстрой фильтрации)
 * @property int $amount //Полная сумма заказа
 * @property int $discount //Скидка по товарам
 * @property int $coupon //Примененная скидка по товару
 * @property int $coupon_id //Купон скидки
 * @property int $delivery_cost //стоимость доставки, включенная в платеж
 * @property int $total //Полная сумма оплаты

 * @property OrderStatus $status //текущий
 * @property OrderStatus[] $statuses
 * @property OrderStep $step
 * @property OrderStep[] $steps
 * @property Payment $payment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property OrderItem[] $items
 */

class Order extends Model
{

    const ONLINE = 701;
    const MANUAL = 702;
    const SHOP = 703;

    protected $fillable = [
        'user_id',
        'type',
        'preorder',
        'paid',
        'finished',

    ];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $user_id, int $type = self::ONLINE, bool $preorder = false): self
    {
        $order = self::make([
            'user_id' => $user_id,
            'paid' => false,

        ]);
        $order->statuse()->create(['status' => OrderStatus::FORMED]);

        $order->save();
        return $order;
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }



    public function isStatus(int $status): bool
    {
        foreach ($this->statuses as $_status) {
            if ($_status->status == $status) return true;
        }
        return false;
    }

    public function setStatus(int $status)
    {
        if ($this->finished)  throw new \DomainException('Заказ закрыт, статус менять нельзя');
        if ($this->isStatus($status)) throw new \DomainException('Статус уже назначен');
        $this->statuses()->create(['status' => $status]);
        if (in_array($status, [OrderStatus::CANCEL, OrderStatus::CANCEL_BY_CUSTOMER, OrderStatus::COMPLETED])) $this->update(['finished' => true]);
    }


    //Relations *************************************************************************************

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function status()
    {
        return $this->hasOne(OrderStatus::class, 'order_id', 'id')->latestOfMany();
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class, 'order_id', 'id');
    }

    public function step()
    {
        return $this->hasOne(OrderStep::class, 'order_id', 'id')->latestOfMany();
    }

    public function steps()
    {
        return $this->hasMany(OrderStep::class, 'order_id', 'id');
    }

//Функции для данных по доставке
//TODO Сделать интерфейс

}
