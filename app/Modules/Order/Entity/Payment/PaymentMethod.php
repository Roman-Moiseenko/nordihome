<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use App\Modules\Base\Entity\Photo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $library //Имя класса
 * @property \App\Modules\Base\Entity\Photo $image
 *
 */

class PaymentMethod extends Model
{

}
