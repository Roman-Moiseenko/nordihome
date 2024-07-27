<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Analytics\Entity\LoggerCron;

use Illuminate\Http\Request;


class CronController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:admin-panel']);
    }

    public function index(Request $request)
    {
        $query = LoggerCron::orderByDesc('created_at');
        $crons = $this->pagination($query, $request, $pagination);
        return view('admin.analytics.cron.index', compact('crons', 'pagination'));
    }

    public function show(LoggerCron $cron)
    {
        return view('admin.analytics.cron.show', compact('cron'));
    }
}
