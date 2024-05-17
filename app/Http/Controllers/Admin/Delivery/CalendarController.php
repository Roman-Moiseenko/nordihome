<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Delivery;

use App\Http\Controllers\Controller;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarPeriod;
use Carbon\Carbon;
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

    public function schedule(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $today = Carbon::now();
            $begin = Carbon::parse($today->year . '-' . $today->month . '-01');
            $month = 0;
            $days = [];
            while ($month < 3) {
                $days[$begin->translatedFormat('F') . ' ' . $begin->year][] = [
                    'day' => $begin->day,
                    'month' => $begin->month,
                    'year' => $begin->year,
                    'disabled' => $begin->lte($today),
                    'week' => $begin->dayOfWeekIso
                    ];
                $begin->addDay();
                if ($begin->year == $today->year) {
                    $month = $begin->month - $today->month;
                } else {
                    $month = (12 - $today->month) + $begin->month;
                }
            }

            return view('admin.delivery.calendar.schedule', compact('days'));
        });

    }
}
