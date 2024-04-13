<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Entity\Admin;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Delivery\Entity\DeliveryOrder;
use App\Modules\Discount\Entity\Discount;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @property int $id
 * @property int $number - номер заказа, присваивается автоматически ++ при отправке на оплату
 * @property int $user_id
 * @property int $type //ONLINE, MANUAL, SHOP, PARSER
 * @property bool $paid //Оплачен (для быстрой фильтрации)
 * @property bool $finished //Завершен (для быстрой фильтрации)
 * @property int $manager_id // Admin::class - менеджер, создавший или прикрепленный к заказу
 * @property int $discount_id //Скидка на заказ - от суммы, или по дням
 * @property float $coupon //Примененная сумма скидки по товару
 * @property int $coupon_id //Купон скидки
 * @property float $manual //Ручная скидка - возможно удалить
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property OrderStatus $status //текущий
 * @property OrderStatus[] $statuses
 * @property OrderAddition[] $additions //Дополнения к заказу (услуги)
 * @property OrderPayment[] $payments //Платежи за заказ

 * @property OrderExpense[] $expenses //Расходники на выдачу товаров и услуг - расчет от $issuances

 * @property OrderItem[] $items
 * @property User $user
 * @property OrderResponsible[] $responsible - удалить
 * @property MovementDocument[] $movements
 * @property Discount $discount
 * @property Admin $manager
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
        'coupon',
        'coupon_id',
        'discount_id',
        'manual',
        'comment',
        'manager_id'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'coupon' => 'float',
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

    #[Deprecated]
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

    public function isPrepaid(): bool
    {
        return $this->status->value == OrderStatus::PREPAID;
    }

    public function isPaid(): bool
    {
        return $this->status->value == OrderStatus::PAID;
    }

    /**
     * Заказ оплачен полностью, но не завершен или отменен
     * @return bool
     */
    public function afterPaid(): bool
    {
        return $this->status->value > OrderStatus::PAID && $this->status->value < OrderStatus::CANCEL;
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
            if (!is_null($item->reserve))
            $item->reserve->update(['reserve_at' => now()->addDays(28)]);
        }
    }

    /**
     * Для каждого товара в заказе назначаем склад резерва
     * @param $storage_id
     * @return void
     */
    #[Deprecated]
    public function setStorage($storage_id)
    {
        foreach ($this->items as $item) {
            $item->reserve->setStorage($storage_id);
            $item->reserve->save();
        }
    }

    public function setManager(int $staff_id)
    {
        if ($this->manager_id != null) throw new \DomainException('Менеджер уже назначен, нельзя менять');
        $this->manager_id = $staff_id;
        $this->save();

        //$this->responsible()->save(OrderResponsible::registerManager($staff->id));
    }

    public function setReserve(Carbon $addDays)
    {
        foreach ($this->items as $item) {
            if (!is_null($item->reserve)) {
                $item->reserve->reserve_at = $addDays;
                $item->reserve->save();
            }
        }
    }

    public function setNumber()
    {
        $count = Order::where('number', '<>', null)->count();
        $this->number = $count + 1;
        $this->save();
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
        if ($this->items()->count() == 0) return now();
        $item = $this->items()->where('preorder', false)->first();
        //TODO Переделать
        if (is_null($item->reserve)) return now(); //throw new \DomainException('Неверный вызов функции! У заказа не установлен резерв');
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
    }

    public function getManager(): ?Admin
    {
        //TODO раскомментить после миграции
        if (is_null($this->manager_id)) return null;
        return $this->manager()->first();

        /** @var OrderResponsible $responsible */
       // $responsible = $this->responsible()->where('staff_post', OrderResponsible::POST_MANAGER)->orderByDesc('created_at')->first();
       // return is_null($responsible) ? null : $responsible->staff;
    }

    //Суммы по заказу*******************
    /**
     * Базовая стоимость всех товаров
     * @return float
     */
    public function getBaseAmount(): float
    {
        $result = 0;
        foreach ($this->items as $item) {
            $result += $item->base_cost * $item->quantity;
        }
        return $result;
    }

    /**
     * Продажная стоимость всех товаров
     * @return float
     */
    public function getSellAmount(): float
    {
        $result = 0;
        foreach ($this->items as $item) {
            $result += $item->sell_cost * $item->quantity;
        }
        return $result;
    }

    /**
     * Скидка по товаром - акции, бонусы
     * @return float
     */
    public function getDiscountProducts(): float
    {
        return $this->getBaseAmount() - $this->getSellAmount();
    }

    /**
     * Скидка на заказ - по сумме, срокам и др.
     * @return float
     */
    public function getDiscountOrder(): float
    {
        if (!is_null($this->discount_id)) {
            /** @var Discount $discount */
            $discount = Discount::find($this->discount_id);
            return $this->getSellAmount() * $discount->discount / 100;
        }
        return 0;
    }

    /**
     * Сумма скидки по купону
     * @return float
     */
    public function getCoupon(): float
    {
        return $this->coupon ?? 0;
    }

    /**
     * Скидка ручная
     * @return float
     */
    public function getManual(): float
    {
        return $this->manual ?? 0;
    }
    /**
     * Сумма всех доп услуг
     * @return float
     */
    public function getAdditionsAmount(): float
    {
        $total_addition = 0;
        foreach ($this->additions as $addition) {
            $total_addition += $addition->amount;
        }
        return $total_addition;
    }

    /**
     * Сборка товара, сумма по всем выбранным позициям из заказа
     * @param int $percent
     * @return float
     */
    public function getAssemblageAmount(int $percent = 15): float
    {
        $assemblage = 0;
        foreach ($this->items as $item) {
            $assemblage += $item->getAssemblage($percent);
        }
        return $assemblage;
    }

    /**
     * Расчет упаковки для товаров, которым требуется
     * @return float
     */
    public function getPackagingAmount(): float
    {
        //TODO на будущее
        return 0;
    }
    /**
     * Итоговая сумма оплаты за заказ, с учетом всех скидок и платежей
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->getAdditionsAmount() +
            $this->getPackagingAmount() +
            $this->getSellAmount() +
            $this->getAssemblageAmount() -
            $this->getDiscountOrder() -
            $this->getCoupon() -
            $this->getManual();
    }

    /**
     * Сумма всех платежей по заказу
     * @return float
     */
    public function getPaymentAmount(): float
    {
        $payments = 0;
        foreach ($this->payments as $payment) {
            $payments += $payment->amount;
        }
        return $payments;
    }

    /**
     * Сумма всех распоряжений
     * @return float
     */
    public function getExpenseAmount(): float
    {
        $amount = 0;
        foreach ($this->expenses as $expense) {
            $amount += $expense->getAmount();
        }
        return $amount;
    }

    /**
     * Общий вес заказа
     * @return float
     */
    public function getWeight(): float
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
    public function getVolume(): float
    {
        $volume = 0;
        foreach ($this->items as $item) {
            $volume += $item->quantity * $item->product->dimensions->volume();
        }
        return $volume;
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
    public function getItemById(int $id_item): OrderItem
    {
        foreach ($this->items as $item) {
            if ($item->id == $id_item) return $item;
        }
        throw new \DomainException('Элемент заказа не найден item_ID = ' . $id_item);
    }

    ///*** Relations *************************************************************************************

    public function manager()
    {
        return $this->belongsTo(Admin::class, 'manager_id', 'id');
    }

    public function expenses()
    {
        return $this->hasMany(OrderExpense::class, 'order_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    #[Deprecated]
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

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }

    public function movements()
    {
        return $this->belongsToMany(MovementDocument::class, 'orders_movements', 'order_id', 'movement_id');
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
        return $this->created_at->translatedFormat('d F');
    }

    public function htmlNum(): string
    {
        if (is_null($this->number)) return 'б/н';
        return '№ ' . str_pad((string)$this->number, 6, '0', STR_PAD_LEFT);
    }

    public function statusHtml(): string
    {
        $comment = '';
        if (!empty($this->status->comment)) $comment = ' (' . $this->status->comment . ')';
        return OrderStatus::STATUSES[$this->status->value] . $comment;
    }

}
