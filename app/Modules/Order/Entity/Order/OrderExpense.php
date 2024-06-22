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
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
 * @property OrderExpenseItem[] $items
 * @property OrderExpenseAddition[] $additions
 * @property Storage $storage
 * @property Order $order
 * @property Admin $staff
 * @property Worker $worker
 * @property CalendarPeriod[] $calendarPeriods
 */
class OrderExpense extends Model
{
    use HtmlInfoData;

    const STATUS_NEW = 1;
    const STATUS_ASSEMBLY = 2;
    const STATUS_ASSEMBLING = 3;
    const STATUS_DELIVERY = 4;
    const STATUS_COMPLETED = 10;

    const DELIVERY_STORAGE = 401;
    const DELIVERY_LOCAL = 402;
    const DELIVERY_REGION = 403;

    const TYPES = [
        '' => 'Неопределенно',
        null => 'Неопределенно',
        self::DELIVERY_STORAGE => 'Самовывоз из магазина',
        self::DELIVERY_LOCAL => 'Доставка по региону',
        self::DELIVERY_REGION => 'Доставка ТК по России',
    ];

    const STATUSES = [
        self::STATUS_NEW => 'Новое',
        self::STATUS_ASSEMBLY => 'Ожидает сборки',
        self::STATUS_ASSEMBLING => 'Собирается',
        self::STATUS_DELIVERY => 'На доставке',
        self::STATUS_COMPLETED => 'Выдано',
    ];

    protected $attributes = [
        'recipient' => '{}',
        'address' => '{}',
    ];
    protected $table = 'order_expenses';
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
     * @return bool
     */
    public function isAssembling(): bool
    {
        return $this->status == self::STATUS_ASSEMBLING;
    }

    /**
     * На доставке по РФ Почтой, есть трек номер
     * @return bool
     */
    public function isDelivery(): bool
    {
        return $this->status == self::STATUS_DELIVERY;
    }

    public function isCompleted(): bool
    {
        return $this->status == self::STATUS_COMPLETED;
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
    public function completed()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    public function assembly()
    {
        $this->status = self::STATUS_ASSEMBLY;
        $this->save();
    }

    public function setNumber()
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

    public function getQuantity(): int
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
            $result += $item->orderItem->product->dimensions->weight() * $item->quantity;
        }
        return $result;
    }

    public function getVolume(): float
    {
        $result = 0.0;
        foreach ($this->items as $item) {
            $result += $item->orderItem->product->dimensions->volume() * $item->quantity;
        }
        return $result;
    }

    //*** RELATIONS
    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(OrderExpenseItem::class, 'expense_id', 'id');
    }

    public function additions()
    {
        return $this->hasMany(OrderExpenseAddition::class, 'expense_id', 'id');
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function calendar():? Calendar
    {

        $period = $this->calendarPeriod();
        return is_null($period) ? null : $period->calendar;
    }

    public function calendarPeriod():? CalendarPeriod
    {
        return $this->calendarPeriods()->first();
    }

    public function calendarPeriods()
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
