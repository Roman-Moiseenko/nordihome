<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Order\Entity\Order\OrderItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use function now;

/**
 * @property int $id
 * @property int $order_item_id
 * @property int $storage_item_id
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $reserve_at
 * @property OrderItem $orderItem
 * @property StorageItem $storageItem
 */
class OrderReserve extends Model
{
    public $timestamps = false;
    protected $table = 'order_reserve';
    protected $fillable = [
        'order_item_id',
        'storage_item_id',
        'quantity',
        'created_at',
        'reserve_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'reserve_at' => 'datetime',
    ];

    public static function register(int $order_item_id, int $storage_item_id, int $quantity, int $minutes): self
    {
        return self::create([
            'order_item_id' => $order_item_id,
            'storage_item_id' => $storage_item_id,
            'quantity' => $quantity,
            'created_at' => now(),
            'reserve_at' => now()->addMinutes($minutes),
        ]);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }

    public function storageItem()
    {
        return $this->belongsTo(StorageItem::class, 'storage_item_id', 'id');
    }
}
