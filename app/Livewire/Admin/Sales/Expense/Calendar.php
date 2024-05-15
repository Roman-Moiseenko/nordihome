<?php

namespace App\Livewire\Admin\Sales\Expense;

use App\Modules\Delivery\Entity\CalendarExpense;
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Modules\Delivery\Service\CalendarService;
use App\Modules\Order\Entity\Order\OrderExpense;
use Carbon\Carbon;
use Livewire\Component;

class Calendar extends Component
{

    public array $days;
    public OrderExpense $expense;
    private Carbon $begin_date;
    public ?int $period = null;

    private CalendarService $service;

    public function boot(CalendarService $service)
    {
        $this->service = $service;
    }

    public function mount(OrderExpense $expense)
    {
        $this->begin_date = now();
        $this->refresh_fields();
        $this->expense = $expense;
        if (!is_null($expense->calendarPeriod())) $this->period = $expense->calendarPeriod()->id;
    }

    public function refresh_fields()
    {
        //$days = $this->service->Nearest();
        $this->days = $this->service->Nearest();
      /*  foreach ($days as $day) {
            $this->days[$day->date_at->format('d-m-Y')][] = [
                'id' => $day->id,
                'period' => $day->periodHtml(),
                'weight' => $day->freeWeight(),
                'volume' => $day->freeVolume(),
                'status' => $day->isDraft(),
            ];
        }*/
    }
    public function set_period()
    {
        //$date, $period
        $period = CalendarPeriod::find($this->period);
        $this->service->attach_expense($period, $this->expense);

        $this->refresh_fields();
    }


    public function render()
    {
        return view('livewire.admin.sales.expense.calendar');
    }
}
