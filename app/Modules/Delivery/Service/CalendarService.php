<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Modules\Analytics\LoggerService;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Modules\Delivery\Entity\DeliveryTruck;
use App\Modules\Order\Entity\Order\OrderExpense;
use Carbon\Carbon;

class CalendarService
{

    private LoggerService $logger;

    public function __construct(LoggerService $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Calendar[]
     */
    public function Nearest(): array
    {
        $this->checkCalendarMonth(now()->month, now()->year);
        $this->checkCalendarMonth(now()->addMonth()->month, now()->addMonth()->year);
        return Calendar::where('date_at', '>', now()->toDateString())->orderBy('date_at')->getModels();
    }

    public function checkCalendarMonth(int $month, int $year)
    {
        $begin_date = Carbon::parse($year . '-' . $month . '-01');

        if (Calendar::where('date_at', '>=', $begin_date->toDateString())->count() > 0) return;

        /** @var DeliveryTruck[] $trucks */
        $trucks = DeliveryTruck::where('active', true)->get();
        $volume = 0;
        $weight = 0;
        foreach ($trucks as $truck) {
            $volume += $truck->volume;
            $weight += $truck->weight;
        }


        for ($i = 1; $i <= $begin_date->endOfMonth()->day; $i++) {
            $day = Carbon::parse($year . '-' . $month . '-' . $i);
            $this->create_date($day, $weight, $volume);
        }
    }

    public function create_date(Carbon $date, float $weight = null, float $volume = null)
    {

        $calendar = Calendar::where('date_at', $date->toDateString())->first();
        if (!is_null($calendar)) return null;
        if ($weight == null || $volume == null) {
            /** @var DeliveryTruck[] $trucks */
            $trucks = DeliveryTruck::where('active', true)->get();
            $volume = 0;
            $weight = 0;
            foreach ($trucks as $truck) {
                $volume += $truck->volume;
                $weight += $truck->weight;
            }
        }
        $calendar = Calendar::register($date);
        foreach (CalendarPeriod::TIMES as $time => $name) {
            $calendar->addPeriod($time, $weight, $volume);
        }
    }

    public function addPeriod(Carbon $date, int $time)
    {
        if (is_null($calendar = Calendar::where('date_at', $date->toDateString())->first())) {
            $calendar = Calendar::register($date);
        }

        /** @var DeliveryTruck[] $trucks */
        $trucks = DeliveryTruck::where('active', true)->get();
        $volume = 0;
        $weight = 0;
        foreach ($trucks as $truck) {
            $volume += $truck->volume;
            $weight += $truck->weight;
        }
        $calendar->addPeriod($time, $weight, $volume);
    }

    public function removePeriod(Carbon $date, int $time)
    {
        /** @var Calendar $calendar */
        $calendar = Calendar::where('date_at', $date->toDateString())->first();
        if (is_null($calendar)) return;

        $period = $calendar->getPeriod($time);
        if (is_null($period)) return;
        if ($period->expenses()->count() > 0) throw new \DomainException('Нельзя удалить данное время, существуют отгрузки');

        $period->delete();
        $calendar->refresh();
        if ($calendar->periods()->count() == 0) $calendar->delete();
    }

    /*
    public function get_day(Carbon $date)
    {
        $this->create_date($date);
        return Calendar::where('date_at', $date->toDateString())->orderBy('date_at')->getModels();
    }
*/
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

        $this->logger->logOrder($expense->order, 'Установлена дата отгрузки', $calendarPeriod->calendar->htmlDate(),  $calendarPeriod->timeHtml());

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

    //public f

}
