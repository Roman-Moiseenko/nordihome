<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $value
 * @property Carbon $created_at
 * @property string $comment
 */
//Статусы для админки, клиенты их не видят
class DeliveryStatus extends Model
{
    const WAIT_STAFF = 1101;
    const WAIT_PRODUCT = 1102;
    const ORDER_PREPARED = 1103;
    const ORDER_COMPLETED = 1104;
    const WAIT_SHIPMENT = 1105;
    const SHIPMENT = 1106;
    const DELIVERY = 1107;
    const ISSUED = 1108;


    const STATUSES = [
        self::WAIT_STAFF => 'В ожидании назначения ответственного',
        self::WAIT_PRODUCT => 'В ожидании поступления товара',
        self::ORDER_PREPARED => 'Собирается',
        self::ORDER_COMPLETED => 'Собран',

        self::WAIT_SHIPMENT => 'Ожидание отгрузки',
        self::SHIPMENT => 'Отгружен',
        self::DELIVERY => 'Доставлен клиенту',
        self::ISSUED => 'Выдан клиенту',
    ];

    protected $fillable = [
        'order_id',
        'value',
        'comment'
    ];

    public $timestamps = false;
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
