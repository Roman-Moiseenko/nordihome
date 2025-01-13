<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $period_id
 * @property int $expense_id
 * @property CalendarPeriod $period
 * @property OrderExpense $expense
 */
class CalendarExpense extends Model
{
    public $timestamps = false;
    protected $table = 'calendars_expenses';

    public function period()
    {
        return $this->belongsTo(CalendarPeriod::class, 'period_id', 'id');
    }

    public function expense()
    {
        return $this->belongsTo(OrderExpense::class, 'expense_id', 'id');
    }
}
