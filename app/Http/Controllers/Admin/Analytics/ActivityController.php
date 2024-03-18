<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Entity\LoggerActivity;
use App\Modules\Analytics\Entity\LoggerCron;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ActivityController extends Controller
{
    private mixed $pagination;

    public function __construct()
    {
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $query = LoggerActivity::orderByDesc('created_at');

            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $activities = $query->paginate($pagination);
                $activities->appends(['p' => $pagination]);
            } else {
                $activities = $query->paginate($this->pagination);
            }

            return view('admin.analytics.activity.index', compact('activities', 'pagination'));

        });
    }
/*
    public function show(LoggerCron $cron)
    {
        return $this->try_catch_admin(function () use ($cron){
            return view('admin.analytics.cron.show', compact('cron'));
        });

    }*/
}
