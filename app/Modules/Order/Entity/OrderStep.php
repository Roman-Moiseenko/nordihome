<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property Carbon $created_at
 * @property int $status //$type
 * @property string $comment
 */
class OrderStep extends Model
{
    const STATUSES = [
        'Создан',
        'Передан в службу формирования заказов',
        'Сформирован заказ на получение',
        'Проходит таможенный контроль',
        'Доставлен на склад',
        'Готов к выдаче',
        'Готов для отправки ТК',
        'Передан в службу доставки ТК',
        'Выдан',
        'Сформирован для отгрузки по региону',
        'Отменен',
        ];



}
