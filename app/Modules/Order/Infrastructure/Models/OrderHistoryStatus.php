<?php
declare(strict_types=1);

namespace App\Modules\Order\Infrastructure\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use setasign\Fpdi\PdfParser\Filter\Lzw;
use function now;

/**
 * @property int $id
 * @property int $order_id
 * @property string $value
 * @property Carbon $created_at
 * @property string $comment
 * @property string $number_document
 * @property string $date_document
 */

class OrderHistoryStatus extends Model
{
    protected $table = 'order_statuses';
    ///Стартовые статусы

    const string NEW = 'new'; //Новый заказ
    const string IN_WORK = 'in_work'; //В работе у менеджера
    const string AWAITING = 'awaiting'; //Ожидает оплаты - резерв 3 дня ??????
    const string PREPAID = 'prepaid';  //Предоплата
    const string PAID = 'paid';  //Оплачен

    const string SHIPPED = 'partially_shipped'; //Частично выдан
    ///Отмененные статусы
    const string CANCELLED = 'cancelled';//



    //Завершен успешно
    const string COMPLETED = 'completed'; //Выдан (завершен)
    const string COMPLETED_REFUND = 'partially_returned'; //Выдан частично, с возвратом части товара (завершен)
    const string RETURNED = 'returned'; //Полный возврат денег (завершен)


    const array STATUSES = [
        self::NEW => 'Сформирован',
        self::IN_WORK => 'В работе у менеджера',
        self::AWAITING => 'Ожидает оплаты',
        self::PREPAID => 'Внесена предоплата',
        self::PAID => 'Оплачен',
        self::SHIPPED => 'Выдан частично',

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
        'comment',
        'date_document',
        'number_document',
    ];

    public $timestamps = false;
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function value(): string
    {
        return self::STATUSES[$this->value];
    }
    public function name(): string
    {
        return self::STATUSES[$this->value];
    }

    protected static function boot()
    {
        parent::boot();
        self::saving(function (OrderHistoryStatus $status) {
            $status->created_at = now();
        });
    }


}
