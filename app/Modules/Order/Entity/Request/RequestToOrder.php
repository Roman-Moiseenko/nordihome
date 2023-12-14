<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Request;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 */
class RequestToOrder extends Model
{

    //TODO Заявка на заказ к Order
    public static function register(int $order_id)
    {

    }
}
