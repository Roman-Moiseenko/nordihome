<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $order_id
 * @property int $status
 * @property Carbon $created_at
 */

class OrderStatus extends Model
{

    const FORMED = 201; //Резерв 1ч
    const AWAITING = 202; //Ожидает оплаты - резерв 3 дня ??????
    const PAID = 203;  //Оплачен
    const DELIVERED = 204; //Отгружен на доставку

    //Завершенные статусы
    const COMPLETED = 205; //Выдан (завершен)
    const CANCEL = 211;//
    const CANCEL_BY_CUSTOMER = 212;//

    protected $fillable = [
        'order_id',
        'status'
    ];
    public $timestamps = false;
    protected $casts = [
        'created_at' => 'datetime',
    ];


    protected static function boot()
    {
        parent::boot();
        self::saving(function (OrderStatus $status) {
            $status->created_at = now();
        });
    }
}
