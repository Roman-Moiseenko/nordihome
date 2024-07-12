<?php

namespace App\Listeners;

use App\Events\ExpenseHasAssembly;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationExpenseAssembly
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(ExpenseHasAssembly $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_LOGGER);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Поступило распоряжение на сборку',
                $event->expense->htmlNumDate(),
                route('admin.order.expense.show', $event->expense),
                'truck'
            ));
        }

    }
}
