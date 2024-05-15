<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Delivery;

use App\Http\Controllers\Controller;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarPeriod;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $filter = $request['filter'] ?? 'new';

            $query = match ($filter) {
                'new' => Calendar::orderBy('date_at')->whereHas('periods', function ($query) {
                    $query->where('status', '<>', CalendarPeriod::STATUS_COMPLETED);
                }),
                'completed' => Calendar::orderByDesc('date_at')->whereHas('periods', function ($query) {
                    $query->where('status', CalendarPeriod::STATUS_COMPLETED);
                }),
                default => Calendar::orderByDesc('date_at'),
        };
            $calendars = $this->pagination($query, $request, $pagination);


            return view('admin.delivery.calendar.index', compact('calendars', 'filter', 'pagination'));
        });
    }
}
