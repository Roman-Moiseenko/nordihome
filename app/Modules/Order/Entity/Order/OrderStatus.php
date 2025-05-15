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
    const int DRAFT = 199; //Резерв нет --- ????

    const int FORMED = 200; //Резерв 1ч
    const int SET_MANAGER = 201; //В работе у менеджера
    const int AWAITING = 202; //Ожидает оплаты - резерв 3 дня ??????
    const int PREPAID = 203;  //Предоплата
    const int PAID = 204;  //Оплачен

    ///Предзаказ
    const int ISSUED_SELLER = 210; //Оформлен у поставщика
    const int ON_PACKAGE = 211; //На комплектации
    const int CUSTOMS = 212; //'Проходит таможенный контроль',
    const int WAREHOUSE = 213; //'Доставлен на склад',

    ///Служба заказов
    const int ORDER_SERVICE = 220;//'Передан в службу заказов',
    const int ORDER_COMPLETED = 221; //'Заказ собран',

    ///Доставка
    const int DELIVERY_REGION = 240; //Готов для отправки ТК
    const int DELIVERY_REGION_SERVICE = 241; //Передан в службу доставки ТК
    const int DELIVERY_LOCAL = 250; //Готов для отправки по региону
    const int DELIVERY_LOCAL_SEND = 251; //Отправлен

    ///Выдача
    const int READY = 260;// 'Готов к выдаче'


    ///Отмененные статусы
    const int CANCEL = 280;//
    const int CANCEL_BY_CUSTOMER = 281;//
    const int REFUND = 282; //Возврат денег

    //Завершен успешно
    const int COMPLETED = 290; //Выдан (завершен)
    const int COMPLETED_REFUND = 291; //Выдан частично, с возвратом части товара (завершен)

    const array STATUSES = [
        self::FORMED => 'Сформирован',
        self::SET_MANAGER => 'В работе у менеджера',
        self::AWAITING => 'Ожидает оплаты',
        self::PREPAID => 'Внесена предоплата',
        self::PAID => 'Оплачен',

        self::COMPLETED => 'Завершен',
        self::COMPLETED_REFUND => 'Завершен с возвратом',
        self::CANCEL => 'Отменен',
        self::CANCEL_BY_CUSTOMER => 'Отменен клиентом',
        self::REFUND => 'Возврат оплаты',
    ];

    const array CONDITIONS = [
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
