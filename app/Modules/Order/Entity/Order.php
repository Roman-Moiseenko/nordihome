<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property bool $paid
 * @property OrderStatus $status //текущий
 * @property int $amount
 * @property int $discount
 * @property int $coupon
 * @property int $coupon_id
 * @property int $payment_id
 * @property OrderStatus[] $statuses
 * @property bool $finished
 * @property string $delivery_class //Local и Transport
 * @property int $delivery_id
 */

class Order extends Model
{

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class, 'order_id', 'id');
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

    public function status()
    {
        return $this->hasOne(OrderStatus::class, 'order_id', 'id')->latestOfMany();
    }

//Функции для данных по доставке
//TODO Сделать интерфейс

}
