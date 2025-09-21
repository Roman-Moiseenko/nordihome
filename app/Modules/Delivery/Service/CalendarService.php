<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Modules\Admin\Entity\Worker;
use App\Modules\Analytics\LoggerService;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Modules\Delivery\Entity\DeliveryTruck;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseWorker;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Deprecated;

class CalendarService
{

    private LoggerService $logger;

    public function __construct(LoggerService $logger)
    {
        $this->logger = $logger;
    }


    private function PeriodToArray(CalendarPeriod $period): array
    {
        return [
            'id' => $period->id,
            'weight' => $period->weight,
            'volume' => $period->volume,
            'free_weight' => $period->freeWeight(),
            'free_volume' => $period->freeVolume(),
            'is_full' => $period->isFull(),
            'time_text' => $period->timeHtml(),
        ];
    }

    #[Deprecated]
    public function checkCalendarMonth(int $month, int $year): void
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

    public function create_date(Carbon $date, float $weight = null, float $volume = null): Calendar
    {

        $calendar = Calendar::where('date_at', $date->toDateString())->first();
        if (!is_null($calendar)) return $calendar;
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
        return $calendar;
    }

    #[Deprecated]
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

    #[Deprecated]
    public function removePeriod(Carbon $date, int $time)
    {
        DB::transaction(function () use ($date, $time) {
            /** @var Calendar $calendar */
            $calendar = Calendar::where('date_at', $date->toDateString())->first();
            if (is_null($calendar)) return;

            $period = $calendar->getPeriod($time);
            if (is_null($period)) return;
            if ($period->expenses()->count() > 0) throw new \DomainException('Нельзя удалить данное время, существуют отгрузки');

            $period->delete();
            $calendar->refresh();
            if ($calendar->periods()->count() == 0) $calendar->delete();
        });

    }


    public function attach_expense(OrderExpense $expense, int $period_id): void
    {
        $calendarPeriod = CalendarPeriod::find($period_id);

        DB::transaction(function () use ($calendarPeriod, $expense) {
            $previousCalendarPeriod = $expense->calendarPeriod;
            $expense->calendarPeriods()->detach();
            $expense->calendarPeriods()->attach($calendarPeriod->id);
            $old = $previousCalendarPeriod == null ? '' : $previousCalendarPeriod->timeHtml();
            if (!is_null($previousCalendarPeriod)) {
                $previousCalendarPeriod->refresh();
                $this->check_full($previousCalendarPeriod);
            }
            $calendarPeriod->refresh();
            $this->check_full($calendarPeriod);
            //Если есть Доставщик и сборщик, отменить
            OrderExpenseWorker::where('expense_id', $expense->id)->where('work', '<>', Worker::WORK_LOADER)->delete();
            $this->logger->logOrder(order: $expense->order, action: 'Установлена дата отгрузки',
                object: $calendarPeriod->calendar->htmlDate(), value: $calendarPeriod->timeHtml(),
                old: $old);
        });
    }

    private function check_full(CalendarPeriod $calendarPeriod): void
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

    public function getDayPeriods(?\Illuminate\Support\Carbon $date)
    {
        //TODO Добавить правило (например кроме выходных), тогда return null;

        $calendar = Calendar::where('date_at', $date->toDateString())->first();
        if (is_null($calendar)) $calendar = $this->create_date($date);
        if ($calendar->isBlocked()) return null;
        return $calendar->periods()->get()->map(fn(CalendarPeriod $period) => $this->PeriodToArray($period));
    }

}
