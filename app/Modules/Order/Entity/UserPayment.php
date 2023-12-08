<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property bool $persona //true - физ.лицо, false - юр.лицо
 * @property string $data_json //для физ.лица ФИО, для юр.лица - реквизиты
 * @property string $default_payment
 */
class UserPayment extends Model
{

}
