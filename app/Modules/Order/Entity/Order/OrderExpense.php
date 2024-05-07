<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Casts\FullNameCast;
use App\Entity\FullName;
use App\Entity\GeoAddress;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Delivery\Entity\DeliveryOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Отгрузки
 * @property int $id
 * @property int $number
 * @property int $order_id
 * @property int $storage_id
 * @property int $staff_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $status
 * @property string $comment
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
 */
class OrderExpense extends Model
{
    const STATUS_NEW = 1;
    const STATUS_ASSEMBLY = 2;
    const STATUS_ASSEMBLING = 3;
    const STATUS_DELIVERY = 4;
    const STATUS_COMPLETED = 10;

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
        'address' => GeoAddress::class,
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
    //*** SET-...
    public function completed()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    public function setNumber()
    {
        $count = OrderExpense::where('number', '<>', null)->count();
        $this->number = $count + 1;
        $this->save();
    }

    //*** GET-...
    public function getAmount()
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

    //*** RELATIONS
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



    //*** Хелперы

    public function statusHTML(): string
    {
        return self::STATUSES[$this->status];
    }

    public function htmlNum(): string
    {
        if (is_null($this->number)) return 'б/н';
        return '№ ' . str_pad((string)$this->number, 6, '0', STR_PAD_LEFT);
    }

    public function typeHTML(): string
    {
        if (is_null($this->type)) return 'Не выбрано';
        return DeliveryOrder::TYPES[$this->type];
    }


}
