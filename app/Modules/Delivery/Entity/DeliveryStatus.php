<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $delivery_id
 * @property int $value
 * @property Carbon $created_at
 * @property string $comment
 */
//Статусы для админки, клиенты их не видят
class DeliveryStatus extends Model
{
    const EMPTY = 1100;
    const WAIT_STAFF = 1101;
    const WAIT_PRODUCT = 1102;
    const ORDER_PREPARED = 1103;
    const ORDER_COMPLETED = 1104;

    const STATUSES = [
        self::EMPTY => 'В обработке',
        self::WAIT_STAFF => 'В ожидании назначения ответственного',
        self::WAIT_PRODUCT => 'В ожидании поступления товара',
        self::ORDER_PREPARED => 'Собирается',
        self::ORDER_COMPLETED => 'Собран',

    ];

    protected $fillable = [
        'delivery_id',
        'value',
        'comment'
    ];

    public $timestamps = false;
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function value(): string
    {
        return self::STATUSES[$this->value];
    }

    protected static function boot()
    {
        parent::boot();
        self::saving(function (DeliveryStatus $status) {
            $status->created_at = now();
        });
    }
}
