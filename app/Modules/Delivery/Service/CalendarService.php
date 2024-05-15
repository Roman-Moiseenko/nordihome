<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Modules\Delivery\Entity\Calendar;
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

        if ($count < 5) {
            //TODO Создать новые дни по графику, см. выходные или без
            if ($count == 0) {
                $begin = now()->addDay();
            } else {
                $begin = Carbon::parse($array[0])->addDay();
            }
            for ($i = 0; $i < 5 - $count; $i++) {
                $this->create_date($begin->addDays($i));
            }
        }
        $calendars = Calendar::where('date_at', '>', now()->toDateString())->orderBy('date_at')->take(10)->getModels();
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
        foreach (Calendar::PERIODS as $period => $name) {
            Calendar::register($date, $period, $weight, $volume);
        }
    }

    public function get_day(Carbon $date)
    {
        $this->create_date($date);
        return Calendar::where('date_at', $date->toDateString())->orderBy('date_at')->getModels();
    }

    public function attach_expense(Calendar $calendar, OrderExpense $expense)
    {
        $old_calendar = $expense->calendar();
        $expense->calendars()->detach();
        $expense->calendars()->attach($calendar->id);

        $old_calendar->refresh();
        $calendar->refresh();
        $this->check_full($old_calendar);
        $this->check_full($calendar);
    }

    private function check_full(Calendar $calendar)
    {
        if ($calendar->isCompleted()) throw new \DomainException('Доставка завершена, внесение изменений невозможно');
        if (
            ($calendar->freeWeight() < $calendar->weight / 10) ||
            ($calendar->freeVolume() < $calendar->volume / 10)
        ) {
            $calendar->status = Calendar::STATUS_FULL;
        } else {
            $calendar->status = Calendar::STATUS_DRAFT;
        }
        $calendar->save();
    }


}
