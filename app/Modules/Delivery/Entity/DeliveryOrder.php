<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Order\Entity\Order\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $type
 * @property float $cost
 * @property string $address
 * @property int $point_storage_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property DeliveryStatus $status Текущий статус
 * @property DeliveryStatus[] $statuses
 * @property Order $order
 * @property Storage $point Точка (склад) выдачи/сбора товара
 */


//TODO Добавить поля
// Накладная, Трек, ТК, время доставки - добавляются ответственным Responsible

class DeliveryOrder extends Model
{

    const STORAGE = 401;
    const LOCAL = 402;
    const REGION = 403;

    const TYPES = [
        self::STORAGE => 'Самовывоз из магазина',
        self::LOCAL => 'Доставка по региону',
        self::REGION => 'Доставка ТК по России',
    ];

    protected $fillable =[
        'order_id',
        'type',
        'address',
        //'cost',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cost' => 'float',
    ];

    public static function register(int $order_id, int $type, string $address): self
    {
        $delivery = self::create([
            'order_id' => $order_id,
            'type' => $type,
            'address' => $address,
        ]);

        $delivery->statuses()->create([
            'value' => DeliveryStatus::WAIT_STAFF,
        ]);
        return $delivery;
    }

    public function setCost(float $cost)
    {
        $this->cost = $cost;
        $this->save();
    }

    public function point()
    {
        if (is_null($this->point_storage_id)) return null;
        return $this->belongsTo(Storage::class, 'point_storage_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function status()
    {
        return $this->hasOne(DeliveryStatus::class, 'delivery_id', 'id')->latestOfMany();
    }

    public function statuses()
    {
        return $this->hasMany(DeliveryStatus::class, 'delivery_id', 'id');
    }

    public function typeHTML()
    {
        if (empty($this->type)) return '';
        return self::TYPES[$this->type];
    }

    public function isStorage(): bool
    {
        return $this->type == self::STORAGE;
    }

    public function isLocal(): bool
    {
        return $this->type == self::LOCAL;
    }

    public function isRegion(): bool
    {
        return $this->type == self::REGION;
    }
}
