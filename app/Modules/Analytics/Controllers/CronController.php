<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Analytics\Entity\LoggerCron;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class CronController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:admin-panel']);
    }

    public function index(Request $request): Response
    {
        $crons = LoggerCron::orderByDesc('created_at')->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(LoggerCron $cron) => $cron->toArray());

        return Inertia::render('Analytics/Cron/Index', [
            'crons' => $crons,
        ]);
    }

    public function show(LoggerCron $cron): Response
    {
        //return view('admin.analytics.cron.show', compact('cron'));

        return Inertia::render('Analytics/Cron/Show', [
            'cron' => array_merge($cron->toArray(), [
                'items' => $cron->items()->get()->toArray(),
            ]),
        ]);
    }
}
