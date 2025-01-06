<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Analytics\Entity\LoggerOrder;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Entity\Discount;
use App\Modules\Order\Entity\OrderReserve;
use App\Modules\Product\Entity\Product;
use App\Modules\Service\Entity\Report;
use App\Modules\User\Entity\User;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\ExpectedValues;
use JetBrains\PhpStorm\Pure;

/**
 * @property int $id
 * @property int $number - номер заказа, присваивается автоматически ++ при отправке на оплату
 * @property int $user_id
 * @property int $organization_id
 * @property int $type //ONLINE, MANUAL, SHOP, PARSER
 * @property bool $paid //Оплачен (для быстрой фильтрации)
 * @property bool $finished //Завершен (для быстрой фильтрации)
 * @property int $staff_id // Admin::class - менеджер, создавший или прикрепленный к заказу
 * @property int $discount_id //Скидка на заказ - от суммы, или по дням
 * @property int $discount_amount //Скидка в рублях для фиксации конечного значения
 * @property float $coupon_amount //Примененная сумма скидки по товару
 * @property int $coupon_id //Купон скидки
 * @property float $manual //Сумма ручных скидок по всем товарам, кроме акционных.
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property OrderStatus $status //текущий
 * @property OrderStatus[] $statuses
 * @property OrderAddition[] $additions //Дополнения к заказу (услуги)
 * @property OrderPayment[] $payments //Платежи за заказ
 * @property OrderPayment $payment //Последний платежи за заказ
 * @property OrderExpense[] $expenses //Расходники на выдачу товаров и услуг - расчет от $issuances
 * @property OrderItem[] $items
 * @property User $user Клиент покупатель
 * @property Organization $organization Организация продавец
 * @property OrderResponsible[] $responsible - удалить
 * @property MovementDocument[] $movements
 * @property Discount $discount
 * @property Admin $staff
 * @property Coupon $coupon
 * @property OrderRefund $refund
 * @property LoggerOrder[] $logs
 * @property Report $invoice
 */

class Order extends Model
{
    use HtmlInfoData;

    const ONLINE = 701;
    const MANUAL = 702;
    const SHOP = 703;
    const PARSER = 704;
    const TYPES = [
        self::ONLINE => 'Интернет-магазин',
        self::MANUAL => 'Менеджер',
        self::SHOP => 'Магазин',
        self::PARSER => 'Парсер',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'paid',
        'finished',
        'coupon_amount',
        'coupon_id',
        'discount_id',
        'discount_amount',
        'manual',
        'comment',
        'staff_id',
        'organization_id',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'coupon_amount' => 'float',
    ];

    public static function register(int|null $user_id, int $type, int $organization_id): self
    {
        $order = self::create([
            'user_id' => $user_id,
            'type' => $type,
            'paid' => false,
            'organization_id' => $organization_id,
        ]);
        $order->statuses()->create(['value' => OrderStatus::FORMED]);
        return $order;
    }

    ///*** ПРОВЕРКА СОСТОЯНИЙ is...()

    /**
     * Статус $value был применен
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

    public function inWork(): bool
    {
        return $this->status->value >= OrderStatus::PREPAID && $this->status->value < OrderStatus::CANCEL;
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

    public function isCompleted(bool $only = false): bool
    {
        if ($only) return $this->status->value == OrderStatus::COMPLETED;
        return $this->status->value >= OrderStatus::COMPLETED;
    }

    public function isCanceled(): bool
    {
        return $this->status->value >= OrderStatus::CANCEL && $this->status->value < OrderStatus::COMPLETED;
    }

    ///*** SET-еры
    public function setStatus(
        #[ExpectedValues(valuesFromClass: OrderStatus::class)] int $value,
        string $comment = ''
    ): void
    {
        if ($this->finished && $value != OrderStatus::COMPLETED_REFUND) throw new \DomainException('Заказ закрыт, статус менять нельзя');
        if ($this->isStatus($value)) throw new \DomainException('Статус уже назначен');
        if ($this->status->value > $value) throw new \DomainException('Нарушена последовательность статусов');

        $this->statuses()->create(['value' => $value, 'comment' => $comment]);

        if (in_array($value, [
            OrderStatus::CANCEL,
            OrderStatus::CANCEL_BY_CUSTOMER,
            OrderStatus::COMPLETED,
            OrderStatus::COMPLETED_REFUND,
            OrderStatus::REFUND
            ])) $this->update(['finished' => true]);
        if ($value == OrderStatus::PAID) $this->update(['paid' => true]);
    }

    public function setPaid(): void
    {
        $this->setStatus(OrderStatus::PAID);
        $this->paid = true;
        $this->save();
        //Увеличиваем резерв на оплаченные товары
    }

    public function setManager(int $staff_id): void
    {
        if ($this->staff_id != null) throw new \DomainException('Менеджер уже назначен, нельзя менять');
        $this->staff_id = $staff_id;
        $this->save();
    }

    public function setReserve(Carbon $addDays): void
    {
        foreach ($this->items as $item) {
            $item->updateReserves($addDays);
        }
    }

    public function setNumber(): void
    {
        $count = Order::where('number', '<>', null)->count();
        $this->number = $count + 1;
        $this->save();
    }

    ///*** GET-еры

    /**
     * Доступные статусы для текущего заказа, ограниченные сверху
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

    public function getQuantity(): float
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->quantity;
        }
        return $quantity;
    }

    #[Pure]
    public function getQuantityExpense(): float
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->getExpenseAmount();
        }
        return $quantity;
    }

    public function getType(): string
    {
        return self::TYPES[$this->type];
    }

    public function getReserveTo(): ?Carbon
    {
        /** @var OrderItem $item */
        if ($this->items()->count() == 0) return now();
        $item = $this->items()->where('preorder', false)->first();

        if (is_null($item) || is_null($item->reserves)) return now();
        /** @var OrderReserve $reserve */
        $reserve = $item->reserves()->first();
        if (empty($reserve)) return null;
        return $reserve->reserve_at;
    }

    /**
     * Получить элемент заказа по Товару и типу (в наличии/на заказ)
     * @param Product $product
     * @param bool $preorder
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
        if (is_null($this->staff_id)) return null;
        return $this->staff->first();
    }

    public function getNameManager(bool $short = false): string
    {
        if (is_null($this->staff)) return '-';
        if ($short) {
            return $this->staff->fullname->getShortName();
        } else {
            return $this->staff->fullname->getFullName();
        }

    }


    /**
     * Доля выдачи заказа 0 - не выдан, 1 -выдан 100%
     */
    public function getPercentIssued(): float
    {
        $total = $this->getTotalAmount();
        if ($total == 0) return 0;
        $expense = $this->getExpenseAmount(OrderExpense::STATUS_COMPLETED);
        return $expense / $total;
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
     * Базовая стоимость всех товаров, за исключением Акционных и Бонусных
     */
    public function getBaseAmountNotDiscount(): float
    {
        $result = 0;
        foreach ($this->items as $item) {
            if (is_null($item->discount_id)) $result += $item->base_cost * $item->quantity;
        }
        return $result;
    }

    /**
     * Продажная стоимость всех товаров
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
     */
    public function getDiscountProducts(): float
    {
        return $this->getBaseAmount() - $this->getSellAmount();
    }

    public function getDiscountPromotions(): float
    {
        $result = 0;
        foreach ($this->items as $item) {
            if ($item->product->hasPromotion()) {
                $result += ($item->base_cost - $item->sell_cost) * $item->quantity;

            }
        }
        return $result;

    }

    /**
     * Скидка на заказ - по сумме, срокам и др.
     */
    public function getDiscountOrder(): float
    {
        return $this->discount_amount;

        if (!is_null($this->discount_id)) {
            /** @var Discount $discount */
            $discount = Discount::find($this->discount_id);
            return $this->getSellAmount() * $discount->discount / 100;
        }
        return 0;
    }

    /**
     * Скидка на заказ - по сумме, срокам и др.
     */
    public function getDiscountName(): string
    {
        if (!is_null($this->discount_id)) {
            /** @var Discount $discount */
            $discount = Discount::find($this->discount_id);
            return $discount->name;
        }
        return '';
    }

    /**
     * Сумма скидки по купону
     */
    public function getCoupon(): float
    {
        return $this->coupon_amount ?? 0;
    }


    /**
     * Сумма всех доп услуг
     */
    public function getAdditionsAmount(): float
    {
        $total_addition = 0;
        foreach ($this->additions as $addition) {
            $total_addition += $addition->getAmount() * $addition->quantity;
        }
        return $total_addition;
    }


    /**
     * Итоговая сумма оплаты за заказ, с учетом всех скидок и платежей
     */
    public function getTotalAmount(): float
    {
        return ceil($this->getAdditionsAmount() +
            $this->getSellAmount() +
            $this->getDiscountOrder() -
            $this->getCoupon());
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
     * Сумма всех распоряжений по статусу
     */
    public function getExpenseAmount(#[ExpectedValues(valuesFromClass: OrderExpense::class)] int $status = null): float
    {
        $amount = 0;
        foreach ($this->expenses as $expense) {
            if (is_null($status) || $expense->status == $status)
                $amount += $expense->getAmount();
        }
        return $amount;
    }


    /**
     * Общий вес заказа
     * @return float
     */
    #[Pure]
    public function getWeight(): float
    {
        $weight = 0;
        foreach ($this->items as $item) {
            $weight += $item->quantity * $item->product->weight();
        }
        return $weight;
    }

    /**
     * Общий объем заказа
     * @return float
     */
    #[Pure]
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
     */
    public function getInStock(): array
    {
        return $this->items()->where('preorder', false)->getModels();
    }

    /**
     * Товары из заказа, которые на предзаказ
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

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function invoice(): MorphOne
    {
        return $this->morphOne(Report::class, 'reportable');
    }

    public function refund(): HasOne
    {
        return $this->hasOne(OrderRefund::class, 'order_id', 'id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(OrderExpense::class, 'order_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    #[Deprecated]
    public function responsible()
    {
        return $this->hasMany(OrderResponsible::class, 'order_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this
            ->belongsTo(User::class, 'user_id', 'id')
            ->withDefault(function (User $user) {
                $user->fullname->surname = 'Не установлен';
                $user->email = null;
                $user->phone = '';
            });
    }

    public function additions(): HasMany
    {
        return $this->hasMany(OrderAddition::class, 'order_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    }

    /**
     * Последний платеж
     * @return mixed
     */
    public function payment(): mixed
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id')->latestOfMany();
    }

    public function status(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'order_id', 'id')->latestOfMany();
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(OrderStatus::class, 'order_id', 'id');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }

    public function movements(): BelongsToMany
    {
        return $this->belongsToMany(MovementDocument::class, 'orders_movements', 'order_id', 'movement_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LoggerOrder::class, 'order_id', 'id')->orderByDesc('created_at');
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

    public function statusHtml(): string
    {
        $comment = '';
        if (!empty($this->status->comment)) $comment = ' (' . $this->status->comment . ')';
        return OrderStatus::STATUSES[$this->status->value] . $comment;
    }

    public function userFullName(): string
    {
        return $this->user->fullname->getFullName();
    }

    public function clearReserve()
    {
        foreach ($this->items as $item) {
            $item->clearReserves();
        }
    }


}
