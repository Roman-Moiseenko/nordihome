<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Modules\Delivery\Repository\CalendarRepository;
use App\Modules\Delivery\Service\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    private CalendarService $service;
    private CalendarRepository $repository;

    public function __construct(CalendarService $service, CalendarRepository $repository)
    {
        $this->middleware(['can:order'])->only(['schedule']);
        $this->middleware(['auth:admin', 'can:delivery']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $filter = $request['filter'] ?? 'new';
        $query = $this->repository->getIndex($filter);
        $calendars = $this->pagination($query, $request, $pagination);

        return view('admin.delivery.calendar.index', compact('calendars', 'filter', 'pagination'));
    }

    public function schedule(Request $request)
    {
        $this->service->checkCalendarMonth(Carbon::now()->month, Carbon::now()->year,);
        $this->service->checkCalendarMonth(Carbon::now()->addMonth()->month, Carbon::now()->addMonth()->year,);

        $days = $this->repository->getDays();
        return view('admin.delivery.calendar.schedule', compact('days'));
    }
}
