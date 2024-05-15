<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Delivery;

use App\Http\Controllers\Controller;
use App\Modules\Delivery\Entity\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = Calendar::where('status', '<>', Calendar::STATUS_COMPLETED)->orderBy('date_at');
            $calendars = $this->pagination($query, $request, $pagination);

            return view('admin.delivery.calendar.index', compact('calendars', 'pagination'));
        });
    }
}
