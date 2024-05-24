<?php

namespace App\Listeners;

use App\Events\SupplyHasSent;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationSupplySent
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(SupplyHasSent $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_SUPPLY);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Поступило распоряжение на сборку',
                $event->supply->htmlNumDate(),
                route('admin.accounting.supply.show', $event->supply),
                'folder-pen'
            ));
        }
    }
}
