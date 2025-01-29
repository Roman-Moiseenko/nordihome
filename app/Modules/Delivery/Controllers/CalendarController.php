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
use Inertia\Inertia;

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



    public function get_day(Request $request)
    {
        $periods = $this->service->getDayPeriods($request->date('date'));
        return response()->json($periods);
    }



}
