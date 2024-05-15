<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Modules\Delivery\Entity\DeliveryTruck;
use App\Modules\Order\Entity\Order\OrderExpense;
use Carbon\Carbon;

class CalendarService
{
    /**
     * @return Calendar[]
     */
    public function Nearest(): array
    {
        $array = Calendar::where('date_at', '>=', now()->toDateString())->orderByDesc('date_at')->groupBy('date_at')->pluck('date_at')->toArray();
        $count = count($array);

        if ($count < 6) {
            //TODO Создать новые дни по графику, см. выходные или без
            if ($count == 0) {
                $begin = now()->addDay();
            } else {
                $begin = Carbon::parse($array[0])->addDay();
            }
            for ($i = 0; $i < 6 - $count; $i++) {
                $this->create_date($begin->addDays($i));
            }
        }
        $calendars = Calendar::where('date_at', '>', now()->toDateString())->orderBy('date_at')->take(6)->getModels();
        return $calendars;
    }

    public function create_date(Carbon $date)
    {

        $calendar = Calendar::where('date_at', $date->toDateString())->first();
        if (!is_null($calendar)) return null;
        /** @var DeliveryTruck[] $trucks */
        $trucks = DeliveryTruck::where('active', true)->get();
        //TODO Получаем доступный объем для грузовиков
        // в дальнейшем в соответствии с календарем выезда

        $volume = 0;
        $weight = 0;
        foreach ($trucks as $truck) {
            $volume += $truck->volume;
            $weight += $truck->weight;
        }
        $calendar = Calendar::register($date);
        foreach (CalendarPeriod::TIMES as $time => $name) {
            $calendar->addPeriod($time, $weight, $volume);
        }
    }

    public function get_day(Carbon $date)
    {
        $this->create_date($date);
        return Calendar::where('date_at', $date->toDateString())->orderBy('date_at')->getModels();
    }

    public function attach_expense(CalendarPeriod $calendarPeriod, OrderExpense $expense)
    {
        $previousCalendarPeriod = $expense->calendarPeriod();
        $expense->calendarPeriods()->detach();
        $expense->calendarPeriods()->attach($calendarPeriod->id);

        if (!is_null($previousCalendarPeriod)) {
            $previousCalendarPeriod->refresh();
            $this->check_full($previousCalendarPeriod);
        }
        $calendarPeriod->refresh();
        $this->check_full($calendarPeriod);
    }

    private function check_full(CalendarPeriod $calendarPeriod)
    {
        if ($calendarPeriod->isCompleted()) throw new \DomainException('Доставка завершена, внесение изменений невозможно');
        if (
            ($calendarPeriod->freeWeight() < $calendarPeriod->weight / 10) ||
            ($calendarPeriod->freeVolume() < $calendarPeriod->volume / 10)
        ) {
            $calendarPeriod->status = CalendarPeriod::STATUS_FULL;
        } else {
            $calendarPeriod->status = CalendarPeriod::STATUS_DRAFT;
        }
        $calendarPeriod->save();
    }


}
