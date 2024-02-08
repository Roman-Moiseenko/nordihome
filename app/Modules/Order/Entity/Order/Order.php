<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Delivery\Entity\DeliveryOrder;
use App\Modules\Order\Entity\Payment\Payment;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $type //ONLINE, MANUAL, SHOP
 * @property bool $preorder
 * @property bool $paid //Оплачен (для быстрой фильтрации)
 * @property bool $finished //Завершен (для быстрой фильтрации)
 * @property float $amount //Полная сумма заказа
 * @property float $discount //Скидка по товарам
 * @property float $coupon //Примененная сумма скидки по товару
 * @property int $coupon_id //Купон скидки
 * @property int $delivery_cost //стоимость доставки, включенная в платеж ?? Доставка оплачивается отдельно
 * @property float $total //Полная сумма оплаты
 * @property string $comment

 * @property OrderStatus $status //текущий
 * @property OrderStatus[] $statuses
 * @property Payment $payment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property OrderItem[] $items
 * @property User $user
 * @property DeliveryOrder $delivery
 */

class Order extends Model
{

    const ONLINE = 701;
    const MANUAL = 702;
    const SHOP = 703;
    const PARSER = 704; //???

    const TYPES = [
        self::ONLINE => 'Интернет-магазин',
        self::MANUAL => 'Вручную',
        self::SHOP => 'Магазин',
        self::PARSER => 'Парсер',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'preorder',
        'paid',
        'finished',
        'amount',
        'discount',
        'coupon',
        'coupon_id',
        'delivery_cost',
        'total'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'amount' => 'float',
        'discount' => 'float',
        'coupon' => 'float',
        'delivery_cost' => 'float',
        'total' => 'float',
    ];

    public static function register(int $user_id, int $type = self::ONLINE, bool $preorder = false): self
    {
        $order = self::create([
            'user_id' => $user_id,
            'type' => $type,
            'paid' => false,
            'preorder' => $preorder,
        ]);
        $order->statuses()->create(['value' => OrderStatus::FORMED]);
        return $order;
    }

    public function setFinance(float $amount, float $discount, float $coupon, ?int $coupon_id, int $delivery_cost = 0)
    {
        $this->update([
            'amount' => $amount,
            'discount' => $discount,
            'coupon' => $coupon,
            'coupon_id' => $coupon_id,
            'delivery_cost' => $delivery_cost,
            'total' => ($amount + $delivery_cost - $discount - $coupon),
        ]);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function isStatus(int $value): bool
    {
        foreach ($this->statuses as $status) {
            if ($status->value == $value) return true;
        }
        return false;
    }

    public function setStatus(int $value, string $comment = '')
    {
        if ($this->finished)  throw new \DomainException('Заказ закрыт, статус менять нельзя');
        if ($this->isStatus($value)) throw new \DomainException('Статус уже назначен');
        if ($this->status->value > $value) throw new \DomainException('Нарушена последовательность статусов');

        $this->statuses()->create(['value' => $value, 'comment' => $comment]);

        if (in_array($value, [OrderStatus::CANCEL, OrderStatus::CANCEL_BY_CUSTOMER, OrderStatus::COMPLETED])) $this->update(['finished' => true]);
        if ($value == OrderStatus::PAID) $this->update(['paid' => true]);
    }

    public function getType(): string
    {
        return self::TYPES[$this->type];
    }
    //Relations *************************************************************************************

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

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

    public function checkOutReserve()
    {
        $canceled = true;
        foreach ($this->items as $item) {
            if ($item->reserve_id != null) $canceled = false;
        }
        if (!$this->paid && $canceled) $this->setStatus(OrderStatus::CANCEL, 'Закончилось время резерва');
    }

    public function delivery()
    {
        return $this->hasOne(DeliveryOrder::class, 'order_id', 'id');
    }

    //Хелперы
    public function htmlDate(): string
    {
        return 'Заказ от ' . $this->created_at->translatedFormat('d F');
    }

    public function htmlNum(): string
    {
        return  '№ ' . str_pad((string)$this->id, 6, '0', STR_PAD_LEFT);
    }

    public function statusHtml(): string
    {
        return OrderStatus::STATUSES[$this->status->value] . ' ' . $this->status->comment;
    }

//Функции для данных по доставке
//TODO Сделать интерфейс

}
