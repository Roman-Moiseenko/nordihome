<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity\Local;

use App\Casts\GeoAddressCast;
use App\Entity\GeoAddress;
use App\Modules\Delivery\Entity\Transport\DeliveryStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property float $amount
 * @property Carbon $delivery_at
 * @property bool $finished
 * @property GeoAddress $address
 * @property string $document
 * @property float $weigh
 * @property DeliveryStatus $status
 * @property DeliveryStatus[] $statuses
 */
class Delivery extends Model
{
    protected $table = 'delivery_local';

    protected $fillable = [
        'order_id',
        'amount',
        'delivery_at',
        'address',
        'document',
        'finished'
    ];

    protected $casts = [
        'delivery_at' => 'datetime',
        'address' => GeoAddressCast::class
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
}
