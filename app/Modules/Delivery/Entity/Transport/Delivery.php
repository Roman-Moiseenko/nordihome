<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity\Transport;

use App\Modules\Base\Casts\GeoAddressCast;
use App\Modules\Base\Entity\GeoAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use function now;


/**
 * @property int $id
 * @property int $order_id
 * @property float $amount //Стоимость доставки
 *
 * @property Carbon $delivery_at
 * @property bool $finished
 * @property GeoAddress $address
 * @property string $document
 * @property string $class //Транспортная компания - класс
 * @property float $weigh
 * @property DeliveryStatus $status
 * @property DeliveryStatus[] $statuses
 */
class Delivery extends Model
{
    protected $table = 'delivery_transport';
    protected $fillable = [
        'order_id',
        'amount',
        'delivery_at',
        'address',
        'document',
        'class',
        'finished'
    ];

    protected $casts = [
        'delivery_at' => 'datetime',
        'address' => GeoAddressCast::class,
    ];

    public static function register(int $order_id, float $amount, string $class, string $document): self
    {
        return self::create([
            'order_id' => $order_id,
            'amount' => $amount,
            'delivery_at' => now(),
            'document' => $document,
            'class' => $class
        ]);
    }

    public function isStatus(int $status): bool
    {
        foreach ($this->statuses as $_status) {
            if ($_status->status == $status) return true;
        }
        return false;
    }

    public function setStatus(int $status)
    {
        if ($this->finished) throw new \DomainException('Заказ закрыт, статус менять нельзя');
        if ($this->isStatus($status)) throw new \DomainException('Статус уже назначен');
        $this->statuses()->create(['status' => $status]);
        if (in_array($status, [DeliveryStatus::CANCEL, DeliveryStatus::CANCEL_BY_CUSTOMER, DeliveryStatus::COMPLETED])) $this->update(['finished' => true]);
    }

    //Relations *************************************************

    public function statuses()
    {
        return $this->hasMany(DeliveryStatus::class, 'delivery_id', 'id');
    }

    public function status()
    {
        return $this->hasOne(DeliveryStatus::class, 'delivery_id', 'id')->latestOfMany();
    }


    public static function namespace(): string
    {
        return __NAMESPACE__;
    }


    public function nameType(): string
    {
        $class = __NAMESPACE__ . "\\" . $this->class;
        return $class::name();
    }
}
