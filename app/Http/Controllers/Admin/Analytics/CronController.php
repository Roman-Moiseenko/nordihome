<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Entity\LoggerCron;
use App\UseCase\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CronController extends Controller
{

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $query = LoggerCron::orderByDesc('created_at');
            $crons = $this->pagination($query, $request, $pagination);
            return view('admin.analytics.cron.index', compact('crons', 'pagination'));
        });
    }

    public function show(LoggerCron $cron)
    {
        return $this->try_catch_admin(function () use ($cron){
            return view('admin.analytics.cron.show', compact('cron'));
        });

    }
}
