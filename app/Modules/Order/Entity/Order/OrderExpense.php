<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Accounting\Entity\Storage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Отгрузки
 * @property int $id
 * @property int $order_id
 * @property int $storage_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $status
 * @property OrderExpenseItem[] $items
 * @property OrderExpenseAddition[] $additions
 * @property Storage $storage
 * @property Order $order
 */
class OrderExpense extends Model
{
    //TODO Отгрузки

    const STATUS_DRAFT = -1;
    const STATUS_MOVEMENT = 1;
    const STATUS_ASSEMBLY = 2;
    const STATUS_ASSEMBLING = 3;
    const STATUS_COMPLETED = 10;

    const STATUSES = [
        self::STATUS_DRAFT => 'Черновик',
        self::STATUS_MOVEMENT => 'Ждет перемещения',
        self::STATUS_ASSEMBLY => 'Ожидает сборки',
        self::STATUS_ASSEMBLING => 'Собрано',
        self::STATUS_COMPLETED => 'Выдано',
    ];

    protected $table = 'order_expenses';

    protected $fillable = [
        'order_id',
        'storage_id',
        'status',
    ];


    public static function register(int $order_id, int $storage_id): self
    {
        return self::create([
            'order_id' => $order_id,
            'status' => self::STATUS_DRAFT,
            'storage_id' => $storage_id,
        ]);
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

    public function setPoint(int $storage_id): void
    {
        $this->update(['storage_id' => $storage_id]);
    }

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

    public function setStorage($storage_id)
    {
        foreach ($this->items as $item) {
            $item->orderItem->reserve->setStorage($storage_id);
            $item->orderItem->reserve->save();
        }
    }

    public function statusHTML()
    {
        return self::STATUSES[$this->status];
    }
}
