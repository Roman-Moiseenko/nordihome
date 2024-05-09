<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property Carbon $date_at
 * @property int $period
 * @property int $truck_id
 * @property int $staff_id
 * @property int $status
 */
class Calendar extends Model
{


    const PERIOD_FIRST = 9901;
    const PERIOD_SECOND = 9902;
    const PERIOD_THIRD = 9903;
    const PERIOD_FOURTH = 9904;

    /// ?????
    const STATUS_DRAFT = 9951;
    const STATUS_FULL = 9952;
    const STATUS_COMPLETED = 9953;

    const PERIODS = [
        self::PERIOD_FIRST => '08:00 - 13:00',
        self::PERIOD_SECOND => '14:00 - 19:00',
    ];
}
