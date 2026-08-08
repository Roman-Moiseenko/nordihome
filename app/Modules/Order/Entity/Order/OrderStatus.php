<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use function now;

/**
 * @property int $id
 * @property int $order_id
 * @property string $value
 * @property Carbon $created_at
 * @property string $comment
 */

class OrderStatus extends Model
{
    ///Стартовые статусы

    const string NEW = 'new'; //Новый заказ
    const string DRAFT = 'draft'; //В работе у менеджера
    const string AWAITING = 'awaiting'; //Ожидает оплаты - резерв 3 дня ??????
    const string PREPAID = 'prepaid';  //Предоплата
    const string PAID = 'paid';  //Оплачен


    ///Отмененные статусы
    const string CANCELLED = 'cancelled';//
    const string RETURNED = 'returned'; //Возврат денег

    //Завершен успешно
    const string COMPLETED = 'completed'; //Выдан (завершен)
    const string COMPLETED_REFUND = 'partially_returned'; //Выдан частично, с возвратом части товара (завершен)

    const array STATUSES = [
        self::NEW => 'Сформирован',
        self::DRAFT => 'В работе у менеджера',
        self::AWAITING => 'Ожидает оплаты',
        self::PREPAID => 'Внесена предоплата',
        self::PAID => 'Оплачен',

        self::COMPLETED => 'Завершен',
        self::COMPLETED_REFUND => 'Завершен с возвратом',
        self::CANCELLED => 'Отменен',
        self::RETURNED => 'Возврат оплаты',
    ];
/*
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
*/
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


}
