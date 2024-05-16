<?php

namespace App\Livewire\Admin\Delivery\Calendar;

use Livewire\Component;

class ScheduleWeek extends Component
{
    public int $week;

    public array $WEEKS = [
        'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс',
    ];

    private array $days;

    public bool $checked;

    public function mount(int $week, array $days)
    {
        $this->week = $week;
        $this->refresh_fields();
        $this->days = $days;
    }

    public function refresh_fields()
    {
        /*
        foreach ($this->days as $month_days) {
            foreach ($month_days as $month_day) {
                if ($month_day['week'] == $this->week + 1) {

                }
            }
        } */
    }

    public function save()
    {
        //TODO Возможно сохранять здесь

        $this->dispatch('update-schedule-day', week: $this->week + 1);
    }

    public function render()
    {
        return view('livewire.admin.delivery.calendar.schedule-week');
    }
}
