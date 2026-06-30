<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $library //Имя класса
 * @property \App\Modules\Shared\Infrastructure\Models\Photo $image
 *
 */

class PaymentMethod extends Model
{

}
