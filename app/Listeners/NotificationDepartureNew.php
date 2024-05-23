<?php

namespace App\Listeners;

use App\Events\DepartureHasCompleted;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationDepartureNew
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    public function handle(DepartureHasCompleted $event): void
    {
        $staffs = $this->staffs->getChief();// $this->staffs->getStaffsByCode(Responsibility::MANAGER_ACCOUNTING);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Списание товаров',
                $event->document->htmlNumDate(),
                route('admin.accounting.departure.show', $event->document),
                'folder-output'
            ));
        }

    }
}
