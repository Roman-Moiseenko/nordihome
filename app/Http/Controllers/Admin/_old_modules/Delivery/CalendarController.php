<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\_old_modules\Delivery;

use App\Http\Controllers\Controller;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Modules\Delivery\Service\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function now;
use function view;

class CalendarController extends Controller
{
    private CalendarService $service;

    public function __construct(CalendarService $service)
    {
        $this->middleware(['can:order'])->only(['schedule']);
        $this->middleware(['auth:admin', 'can:delivery']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $filter = $request['filter'] ?? 'new';

            $query = match ($filter) {
                'new' => Calendar::orderBy('date_at')->where('date_at', '>=', now()->toDateString())->whereHas('periods', function ($query) {
                    $query->where('status', '<>', CalendarPeriod::STATUS_COMPLETED);
                }),
                //TODO Убрать пустые дни
                'completed' => Calendar::orderByDesc('date_at')->where('date_at', '<', now()->toDateString())->whereHas('periods', function ($query) {
                    //$query->where('status', CalendarPeriod::STATUS_COMPLETED);
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

            $this->service->checkCalendarMonth(
                Carbon::now()->month,
                Carbon::now()->year,
            );
            $this->service->checkCalendarMonth(
                Carbon::now()->addMonth()->month,
                Carbon::now()->addMonth()->year,
            );

            $today = Carbon::now();
            $begin = Carbon::parse($today->year . '-' . $today->month . '-01');
            $month = 0;
            $days = [];
            while ($month < 2) {
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
