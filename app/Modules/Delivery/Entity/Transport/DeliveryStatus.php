<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity\Transport;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Deprecated;
use function now;

/**
 * @property int $id
 * @property int $delivery_id
 * @property int $status
 * @property Carbon $created_at
 */
#[Deprecated]
class DeliveryStatus extends Model
{

    const NEW = 301;
    //TODO Добавить статусы
    const COMPLETED = 310;
    const CANCEL = 311;
    const CANCEL_BY_CUSTOMER = 312;

    public $timestamps = false;
    protected $fillable = [
        'delivery_id',
        'status',
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        self::saving(function (DeliveryStatus $status) {
            $status->created_at = now();
        });
    }
}
