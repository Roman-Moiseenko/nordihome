<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $calendar_id
 * @property int $expense_id
 *
 * @property Calendar $calendar
 * @property OrderExpense $expense
 */
class CalendarExpense extends Model
{
    public $timestamps = false;
    protected $table = 'calendars_expenses';

    public function calendar()
    {
        return $this->belongsTo(Calendar::class, 'calendar_id', 'id');
    }

    public function expense()
    {
        return $this->belongsTo(OrderExpense::class, 'expense_id', 'id');
    }
}
