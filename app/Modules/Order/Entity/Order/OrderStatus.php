<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use function now;

/**
 * @property int $id
 * @property int $order_id
 * @property int $value
 * @property Carbon $created_at
 * @property string $comment
 */

class OrderStatus extends Model
{
    ///Стартовые статусы
    const DRAFT = 199; //Резерв нет --- ????

    const FORMED = 200; //Резерв 1ч
    const SET_MANAGER = 201; //В работе у менеджера
    const AWAITING = 202; //Ожидает оплаты - резерв 3 дня ??????
    const PREPAID = 203;  //Предоплата
    const PAID = 204;  //Оплачен

    ///Предзаказ
    const ISSUED_SELLER = 210; //Оформлен у поставщика
    const ON_PACKAGE = 211; //На комплектации
    const CUSTOMS = 212; //'Проходит таможенный контроль',
    const WAREHOUSE = 213; //'Доставлен на склад',

    ///Служба заказов
    const ORDER_SERVICE = 220;//'Передан в службу заказов',
    const ORDER_COMPLETED = 221; //'Заказ собран',

    ///Доставка
    const DELIVERY_REGION = 240; //Готов для отправки ТК
    const DELIVERY_REGION_SERVICE = 241; //Передан в службу доставки ТК
    const DELIVERY_LOCAL = 250; //Готов для отправки по региону
    const DELIVERY_LOCAL_SEND = 251; //Отправлен

    ///Выдача
    const READY = 260;// 'Готов к выдаче'
    const COMPLETED = 290; //Выдан (завершен)

    ///Отмененные статусы
    const CANCEL = 280;//
    const CANCEL_BY_CUSTOMER = 281;//
    const REFUND = 282; //Возврат денег

    const STATUSES = [
        self::FORMED => 'Сформирован',
        self::SET_MANAGER => 'В работе у менеджера',
        self::AWAITING => 'Ожидает оплаты',
        self::PAID => 'Оплачен',
        self::ISSUED_SELLER => 'Оформлен у поставщика',
        self::ON_PACKAGE => 'На комплектации',
        self::CUSTOMS => 'Проходит таможенный контроль',
        self::WAREHOUSE => 'Доставлен на склад',
        self::ORDER_SERVICE => 'Передан в службу сборки заказов',
        self::ORDER_COMPLETED => 'Заказ собран',
        self::DELIVERY_REGION => 'Готов для отправки ТК',
        self::DELIVERY_REGION_SERVICE => 'Передан в службу доставки ТК',
        self::DELIVERY_LOCAL => 'Готов для отправки по региону',
        self::DELIVERY_LOCAL_SEND => 'Отправлен',
        self::READY => 'Готов к выдаче',
        self::COMPLETED => 'Выдан',
        self::CANCEL => 'Отменен',
        self::CANCEL_BY_CUSTOMER => 'Отменен клиентом',
        self::REFUND => 'Возврат оплаты',
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

    public function value(): string
    {
        return self::STATUSES[$this->value];
    }

    protected static function boot()
    {
        parent::boot();
        self::saving(function (OrderStatus $status) {
            $status->created_at = now();
        });
    }

    public static function getServiceStatuses(): array
    {
        $result = [];
        foreach (self::STATUSES as $code => $name) {
            if ($code > self::PAID && $code < self::ORDER_SERVICE) {
                $result[$code] = $name;
            }
        }
        return $result;
    }

}
