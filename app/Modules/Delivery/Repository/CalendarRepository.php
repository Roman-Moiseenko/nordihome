<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Repository;

use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarPeriod;
use Carbon\Carbon;

class CalendarRepository
{

    public function getDays(): array
    {

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
        return $days;
    }

    public function getIndex(string $filter)
    {
        return match ($filter) {
            'new' => Calendar::orderBy('date_at')->where('date_at', '>=', now()->toDateString())->whereHas('periods', function ($query) {
                $query->where('status', '<>', CalendarPeriod::STATUS_COMPLETED);
            }),
            'completed' => Calendar::orderByDesc('date_at')->where('date_at', '<', now()->toDateString())->whereHas('periods', function ($query) {
            }),
            default => Calendar::orderByDesc('date_at'),
        };
    }
}
