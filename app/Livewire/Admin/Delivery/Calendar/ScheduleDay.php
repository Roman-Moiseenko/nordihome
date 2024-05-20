<?php

namespace App\Livewire\Admin\Delivery\Calendar;

use Livewire\Attributes\On;
use App\Modules\Delivery\Entity\Calendar as CalendarDay;
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Modules\Delivery\Service\CalendarService;
use Carbon\Carbon;
use Livewire\Component;

class ScheduleDay extends Component
{

    public bool $disabled;
    public Carbon $date;
    public array $periods;
    public ?CalendarDay $calendar;

    public array $times;
    private CalendarService $service;

    public function boot(CalendarService $service)
    {
        $this->times = CalendarPeriod::TIMES;
        $this->service = $service;
    }

    public function mount(int $year, int $month, int $day, bool $disabled)
    {
        $this->date = Carbon::parse($year . '-' . $month . '-' . $day);
        $this->disabled = $disabled;

        $this->refresh_fields();
    }


    public function refresh_fields()
    {
        $this->calendar = CalendarDay::where('date_at', $this->date)->first();
        foreach (CalendarPeriod::TIMES as $key => $name) {
            if (is_null($this->calendar)) {
                $this->periods[$key] = ['checked' => false, 'name' => $name];
            } else {
                $period = $this->calendar->getPeriod($key);
                $this->periods[$key] = ['checked' => !is_null($period), 'name' => $name];
            }
        }
    }
/*
    #[On('update-schedule-day')]
    public function check_all($week)
    {
        if ($this->disabled == false && $this->date->dayOfWeekIso == $week) {
            foreach ($this->periods as $key => &$period) {
                $period['checked'] = true;
            }
            $this->save();
            $this->refresh_fields();
        }
    }
*/
    public function save()
    {
        foreach ($this->periods as $key => $period) {
            if ($period['checked']) {
                $this->service->addPeriod($this->date, $key);
            } else {
                $this->service->removePeriod($this->date, $key);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.delivery.calendar.schedule-day');
    }

    public function exception($e, $stopPropagation)
    {
        if ($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Внимание', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
