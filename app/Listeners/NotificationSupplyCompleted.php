<?php

namespace App\Listeners;

use App\Events\SupplyHasCompleted;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationSupplyCompleted
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(SupplyHasCompleted $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Поступление товара от поставщика',
                $event->supply->distributor->name,
                route('admin.accounting.supply.show', $event->supply),
                'folder-pen'
            ));
        }
    }
}
