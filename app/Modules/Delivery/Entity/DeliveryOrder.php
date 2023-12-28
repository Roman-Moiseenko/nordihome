<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;
use App\Modules\Admin\Entity\Responsible;
use App\Modules\Order\Entity\Order\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\File;

/**
 * @property int $id
 * @property int $order_id
 * @property int $type
 * @property float $cost
 * @property string $address
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property DeliveryStatus $status
 * @property DeliveryStatus[] $statuses
 * @property Responsible $responsible
 * @property Order $order
 */


//TODO Добавить поля
// Накладная, Трек, ТК, время доставки - добавляются ответственным Responsible

class DeliveryOrder extends Model
{

    const STORAGE = 401;
    const LOCAL = 402;
    const REGION = 403;

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

    public function responsible()
    {
        return $this->morphOne(Responsible::class, 'taskable');
    }

    public function setResponsible(int $staff_id)
    {
        //TODO !!!!
    }
}
