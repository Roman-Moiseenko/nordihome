<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Base\Casts\FullNameCast;
use App\Modules\Base\Casts\GeoAddressCast;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarExpense;
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Modules\Delivery\Entity\DeliveryCargo;
use App\Modules\Guide\Entity\Addition;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * Отгрузки
 * @property int $id
 * @property int $number
 * @property int $order_id
 * @property int $storage_id
 * @property int $staff_id
 * @property int $worker_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $status
 * @property string $comment
 * @property string $track
 *
 * @property FullName $recipient
 * @property string $phone
 * @property int $type
 * @property GeoAddress $address
 *
 * @property OrderExpenseRefund[] $refunds
 * @property OrderExpenseItem[] $items
 * @property OrderExpenseAddition[] $additions
 * @property Storage $storage
 * @property Order $order
 * @property Admin $staff
 * @property Worker[] $workers
 * @property CalendarPeriod[] $calendarPeriods
 * @property CalendarPeriod $calendarPeriod
 * @property DeliveryCargo $delivery
 */
class OrderExpense extends Model
{
    use HtmlInfoData;

    const int STATUS_NEW = 1;
    const int STATUS_ASSEMBLY = 2;
    const int STATUS_ASSEMBLING = 3;
    const int STATUS_ASSEMBLED = 4;
    const int STATUS_DELIVERY = 6;
    const int STATUS_DELIVERED = 7;
    const int STATUS_COMPLETED = 10;
    const int STATUS_CANCELED = 11;
    const int STATUS_REFUND = 12;

    const int DELIVERY_STORAGE = 401;
    const int DELIVERY_LOCAL = 402;
    const int DELIVERY_REGION = 403;
    const int DELIVERY_OZON = 404;

    const array TYPES = [
        '' => 'Неопределенно',
        null => 'Неопределенно',
        self::DELIVERY_STORAGE => 'Самовывоз из магазина',
        self::DELIVERY_LOCAL => 'Доставка по региону',
        self::DELIVERY_REGION => 'Доставка ТК по России',
    ];
    const array DELIVERIES = [
        self::DELIVERY_STORAGE => 'Самовывоз из магазина',
        self::DELIVERY_LOCAL => 'Доставка по региону',
        self::DELIVERY_REGION => 'Доставка ТК по России',
    ];

    const array STATUSES = [
        self::STATUS_NEW => 'Новое',
        self::STATUS_ASSEMBLY => 'Ожидает сборки',
        self::STATUS_ASSEMBLING => 'Собирается',
        self::STATUS_ASSEMBLED => 'Собран',
        self::STATUS_DELIVERY => 'На доставке',
        self::STATUS_DELIVERED => 'Доставлен',
        self::STATUS_COMPLETED => 'Выдано',
        self::STATUS_CANCELED => 'Отменен',
        self::STATUS_REFUND => 'Возврат',
    ];

    protected $attributes = [
        'recipient' => '{}',
        'address' => '{}',
    ];
    protected $table = 'order_expenses';
    protected $touches = [
        'order',
    ];
    protected $fillable = [
        'order_id',
        'storage_id',
        'status',
        'comment',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'recipient' => FullNameCast::class,
        'address' => GeoAddressCast::class,
    ];

    public static function register(int $order_id, int $storage_id): self
    {
        return self::create([
            'order_id' => $order_id,
            'status' => self::STATUS_NEW,
            'storage_id' => $storage_id,
        ]);
    }

    //*** IS-...
    //текущий статус
    public function isNew(): bool
    {
        return $this->status == self::STATUS_NEW;
    }

    /**
     * Отправлен на сборку, ждет назначения сборщика
     * @return bool
     */
    public function isAssembly(): bool
    {
        return $this->status == self::STATUS_ASSEMBLY;
    }

    /**
     * Собирается сборщиком - назначен (таблица?)
     */
    public function isAssembling(): bool
    {
        return $this->status == self::STATUS_ASSEMBLING;
    }

    /**
     * Заказ собран, ожидает выдачи или доставки
     */
    public function isAssembled(): bool
    {
        return $this->status == self::STATUS_ASSEMBLED;
    }

    /**
     * На доставке по РФ Почтой, есть трек номер
     */
    public function isDelivery(): bool
    {
        return $this->status == self::STATUS_DELIVERY;
    }

    /**
     * Доставлен. (Отметка доставщика или Почты)
     */
    public function isDelivered(): bool
    {
        return $this->status == self::STATUS_DELIVERED;
    }

    public function isCompleted(): bool
    {
        return $this->status == self::STATUS_COMPLETED || $this->status == self::STATUS_REFUND;
    }

    public function isCanceled(): bool
    {
        return $this->status == self::STATUS_CANCELED;
    }

    public function isRefund(): bool
    {
        return $this->refunds()->count() > 0;
    }

    /**
     * Требуется сборка
     */
    public function isAssemble(): bool
    {
        foreach ($this->additions as $addition) {
            if ($addition->orderAddition->addition->type == Addition::ASSEMBLY) return true;
        }
        return false;
    }

    //тип доставки
    public function isStorage(): bool
    {
        return $this->type == self::DELIVERY_STORAGE;
    }

    public function isLocal(): bool
    {
        return $this->type == self::DELIVERY_LOCAL;
    }

    public function isRegion(): bool
    {
        return $this->type == self::DELIVERY_REGION;
    }

    //*** SET-...
    public function assembly(): void
    {
        $this->status = self::STATUS_ASSEMBLY;
        $this->save();
    }

    public function setNumber(): void
    {
        $count = OrderExpense::where('number', '<>', null)->count();
        $this->number = $count + 1;
        $this->save();
    }

    //*** GET-...
    public function getAmount(): float|int
    {
        $result = 0;
        foreach ($this->items as $item) {
            $result += $item->quantity * $item->orderItem->sell_cost;
        }
        foreach ($this->additions as $addition) {
            $result += $addition->amount;
        }

        return $result;
    }

    public function getQuantity(): float
    {
        $result = 0;
        foreach ($this->items as $item) {
            $result += $item->quantity;
        }

        return $result + $this->additions()->count();
    }

    public function getWeight(): float
    {
        $result = 0.0;
        foreach ($this->items as $item) {
            $result += $item->orderItem->product->weight() * $item->quantity;
        }
        return $result;
    }

    public function getVolume(): float
    {
        $result = 0.0;
        foreach ($this->items as $item) {
            $result += $item->orderItem->product->packages->volume() * $item->quantity;
        }
        return $result;
    }

    public function getWorker( #[ExpectedValues(valuesFromClass: Worker::class)]int $work):? Worker
    {
        foreach ($this->workers as $worker) {
            if ($worker->pivot->work == $work) return $worker;
        }
        return null;
    }

    /**
     * Доставщик
     */
    public function getDriver(): ?Worker
    {
        return $this->getWorker(Worker::WORK_DRIVER);
    }

    /**
     * Сборщик мебели
     */
    public function getAssemble(): array
    {
        $result = [];
        foreach ($this->workers as $worker) {
            if ($worker->pivot->work == Worker::WORK_ASSEMBLE) $result[] = $worker;
        }
        return $result;


    }

    /**
     * Упаковщик (грузчик)
     */
    public function getLoader(): ?Worker
    {
        return $this->getWorker(Worker::WORK_LOADER);
    }

    //*** RELATIONS
    public function refunds(): HasMany
    {
        return $this->hasMany(OrderExpenseRefund::class, 'expense_id', 'id');
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(
            Worker::class, 'order_expenses_workers',
            'expense_id','worker_id')->withPivot(['work']);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderExpenseItem::class, 'expense_id', 'id');
    }

    public function additions(): HasMany
    {
        return $this->hasMany(OrderExpenseAddition::class, 'expense_id', 'id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function calendar():? Calendar
    {
        return is_null($this->calendarPeriod) ? null : $this->calendarPeriod->calendar;
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(DeliveryCargo::class, 'expense_id', 'id');
    }

    public function calendarPeriod(): HasOneThrough
    {
        return $this->hasOneThrough(
            CalendarPeriod::class,
            CalendarExpense::class,
            'expense_id', 'id',
            'id', 'period_id'
        );
    }

    public function calendarPeriods(): BelongsToMany
    {
        return $this->belongsToMany(CalendarPeriod::class, 'calendars_expenses', 'expense_id', 'period_id');
    }

    //*** Хелперы
    public function statusHTML(): string
    {
        return self::STATUSES[$this->status];
    }

    public function typeHTML(): string
    {
        return self::TYPES[$this->type];
    }

}
