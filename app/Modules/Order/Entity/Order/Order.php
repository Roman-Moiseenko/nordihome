<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Entity\Admin;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Delivery\Entity\DeliveryOrder;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @property int $id
 * @property int $user_id
 * @property int $type //ONLINE, MANUAL, SHOP, PARSER
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
 * @property OrderAddition[] $additions //Дополнения к заказу (услуги)
 * @property OrderPayment[] $payments //Платежи за заказ
 * @property OrderExpense[] $expenses //Расходники на выдачу
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property OrderItem[] $items
 * @property User $user
 * @property DeliveryOrder $delivery //Удалить
 * @property OrderResponsible[] $responsible
 * @property MovementDocument[] $movements
 */
class Order extends Model
{
    const ONLINE = 701;
    const MANUAL = 702;
    const SHOP = 703;
    const PARSER = 704;

    const TYPES = [
        self::ONLINE => 'Интернет-магазин',
        self::MANUAL => 'Вручную',
        self::SHOP => 'Магазин',
        self::PARSER => 'Парсер',
    ];

    protected $fillable = [
        'user_id',
        'type',
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

    public static function register(int $user_id, int $type = self::ONLINE): self
    {
        $order = self::create([
            'user_id' => $user_id,
            'type' => $type,
            'paid' => false
        ]);
        $order->statuses()->create(['value' => OrderStatus::FORMED]);
        return $order;
    }

    ///*** ПРОВЕРКА СОСТОЯНИЙ is...()

    /**
     * Статус $value был применен
     * @param int $value
     * @return bool
     */
    public function isStatus(#[ExpectedValues(valuesFromClass: OrderStatus::class)] int $value): bool
    {
        foreach ($this->statuses as $status) {
            if ($status->value == $value) return true;
        }
        return false;
    }

    public function isParser(): bool
    {
        return $this->type == self::PARSER;
    }

    /**
     * Установлена точка выдачи/сборки товара
     * @return bool
     */
    public function isPoint(): bool
    {
        if (is_null($this->delivery)) return false;
        return !is_null($this->delivery->point_storage_id);
    }

    ///***Проверка текущего статуса
    public function isNew(): bool
    {
        return $this->status->value == OrderStatus::FORMED;
    }

    public function isManager(): bool
    {
        return $this->status->value == OrderStatus::SET_MANAGER;
    }

    public function isAwaiting(): bool
    {
        return $this->status->value == OrderStatus::AWAITING;
    }

    public function isPaid(): bool
    {
        return $this->status->value >= OrderStatus::PAID && $this->status->value < OrderStatus::ORDER_SERVICE;
    }

    public function isToDelivery(): bool
    {
        return $this->status->value >= OrderStatus::ORDER_SERVICE && $this->status->value < OrderStatus::CANCEL;
    }

    public function isCompleted(): bool
    {
        return $this->status->value == OrderStatus::COMPLETED;
    }

    public function isCanceled(): bool
    {
        return $this->status->value >= OrderStatus::CANCEL && $this->status->value < OrderStatus::COMPLETED;
    }

    ///*** SET-еры
    public function setFinance(float $amount, float $discount, float $coupon, ?int $coupon_id)
    {
        $this->update([
            'amount' => $amount,
            'discount' => $discount,
            'coupon' => $coupon,
            'coupon_id' => $coupon_id,
            //'delivery_cost' => $delivery_cost,
            'total' => ($amount - $discount - $coupon),
        ]);
    }

    public function setStatus(
        #[ExpectedValues(valuesFromClass: OrderStatus::class)] int $value,
        string $comment = ''
    )
    {
        if ($this->finished) throw new \DomainException('Заказ закрыт, статус менять нельзя');
        if ($this->isStatus($value)) throw new \DomainException('Статус уже назначен');
        if ($this->status->value > $value) throw new \DomainException('Нарушена последовательность статусов');

        $this->statuses()->create(['value' => $value, 'comment' => $comment]);

        if (in_array($value, [OrderStatus::CANCEL, OrderStatus::CANCEL_BY_CUSTOMER, OrderStatus::COMPLETED])) $this->update(['finished' => true]);
        if ($value == OrderStatus::PAID) $this->update(['paid' => true]);
    }

    public function setPaid()
    {
        $this->setStatus(OrderStatus::PAID);
        $this->paid = true;
        $this->save();
        //Увеличиваем резерв на оплаченные товары
        foreach ($this->items as $item) {
            $item->reserve->update(['reserve_at' => now()->addDays(28)]);
        }
    }

    /**
     * Устанавливаем точку выдачи/сборки товара
     * @param int $point_storage_id
     * @return void
     */
    public function setPoint(int $point_storage_id)
    {
        $this->delivery->point_storage_id = $point_storage_id;
        $this->delivery->save();
    }

    /**
     * Для каждого товара в заказе назначаем склад резерва
     * @param $storage_id
     * @return void
     */
    public function setStorage($storage_id)
    {
        foreach ($this->items as $item) {
            $item->reserve->setStorage($storage_id);
            $item->reserve->save();
        }
    }

    ///*** GET-еры

    /**
     * Доступные статусы для текущего заказа, ограниченные сверху
     * @param int $top_status
     * @return array
     */
    public function getAvailableStatuses(int $top_status = OrderStatus::COMPLETED): array
    {
        $last_code = $this->status->value;
        $result = [];
        foreach (OrderStatus::STATUSES as $code => $name) {
            if ($code > $last_code && $code < $top_status) {
                $result[$code] = $name;
            }
        }
        return $result;
    }

    public function getQuantity(): int
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->quantity;
        }
        return $quantity;
    }

    public function getType(): string
    {
        return self::TYPES[$this->type];
    }

    public function getReserveTo():? Carbon
    {
        /** @var OrderItem $item */
        if ($this->items->count() == 0) return now();
        $item = $this->items()->where('preorder', false)->first();
        if (is_null($item->reserve)) throw new \DomainException('Неверный вызов функции! У заказа не установлен резерв');
        return $item->reserve->reserve_at;
    }

    /**
     * Получить элемент заказа по Товару и типу (в наличии/на заказ)
     * @param Product $product
     * @return OrderItem|null
     */
    public function getItem(Product $product, bool $preorder = false): ?OrderItem
    {
        foreach ($this->items as $orderItem) {
            if ($orderItem->product->id == $product->id && $orderItem->preorder == $preorder) return $orderItem;
        }
        return null;
        //throw new \DomainException('Данный товар не содержится в заказе');
    }

    public function getManager(): ?Admin
    {
        /** @var OrderResponsible $responsible */
        $responsible = $this->responsible()->where('staff_post', OrderResponsible::POST_MANAGER)->orderByDesc('created_at')->first();
        return is_null($responsible) ? null : $responsible->staff;
    }

    public function getLogger(): ?Admin
    {
        /** @var OrderResponsible $responsible */
        $responsible = $this->responsible()->where('staff_post', OrderResponsible::POST_LOGGER)->orderByDesc('created_at')->first();
        return is_null($responsible) ? null : $responsible->staff;
    }

    ///*** Relations *************************************************************************************

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function responsible()
    {
        return $this->hasMany(OrderResponsible::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function additions()
    {
        return $this->hasMany(OrderAddition::class, 'order_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    }

    public function status()
    {
        return $this->hasOne(OrderStatus::class, 'order_id', 'id')->latestOfMany();
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class, 'order_id', 'id');
    }

    public function delivery()//TODO переделать на hasMany() если будет ТЗ
    {
        return $this->hasOne(DeliveryOrder::class, 'order_id', 'id');
    }

    public function movements()
    {
        return $this->hasMany(MovementDocument::class, 'order_id', 'id');
    }

    /**
     * Возвращает true если весь товар из заказа не имеет резерва
     * @return bool
     */
    public function checkOutReserve(): bool
    {
        foreach ($this->items as $item) {
            if ($item->reserve_id != null)
            return false;
        }
        return true;
    }

    ///*** Хелперы
    public function htmlDate(): string
    {
        return 'Заказ от ' . $this->created_at->translatedFormat('d F');
    }

    public function htmlNum(): string
    {
        return '№ ' . str_pad((string)$this->id, 6, '0', STR_PAD_LEFT);
    }

    public function statusHtml(): string
    {
        return OrderStatus::STATUSES[$this->status->value] . ' ' . $this->status->comment;
    }

    /**
     * Общий вес заказа
     * @return float
     */
    public function weight(): float
    {
        $weight = 0;
        foreach ($this->items as $item) {
            $weight += $item->quantity * $item->product->dimensions->weight();
        }
        return $weight;
    }

    /**
     * Общий объем заказа
     * @return float
     */
    public function volume(): float
    {
        $volume = 0;
        foreach ($this->items as $item) {
            $volume += $item->quantity * $item->product->dimensions->volume();
        }
        return $volume;
    }

    /**
     * Общая стоимость с учетом всех дополнений
     * @return float
     */
    public function totalPayments(): float
    {
        $total = 0;
        foreach ($this->additions as $addition) {
            $total += $addition->amount;
        }
        return $total + $this->total;
    }

    /**
     * Товары из заказа, которые были по наличию
     * @return OrderItem[]
     */
    public function getInStock(): array
    {
        return $this->items()->where('preorder', false)->getModels();
    }

    /**
     * Товары из заказа, которые на предзаказ
     * @return OrderItem[]
     */
    public function getPreOrder(): array
    {
        return $this->items()->where('preorder', true)->getModels();
    }
}
