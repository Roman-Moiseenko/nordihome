<?php

namespace App\Livewire\Admin\Order\Expense;

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
        $this->days = $this->service->Nearest();
    }
    public function set_period()
    {
        $period = CalendarPeriod::find($this->period);
        $this->service->attach_expense($period, $this->expense);

        $this->refresh_fields();
    }


    public function render()
    {
        return view('livewire.admin.order.expense.calendar');
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
