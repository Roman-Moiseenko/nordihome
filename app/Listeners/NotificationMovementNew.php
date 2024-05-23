<?php

namespace App\Listeners;

use App\Events\MovementHasCreated;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationMovementNew
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    public function handle(MovementHasCreated $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ACCOUNTING);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                "Новое перемещение",
                $event->document->htmlNumDate(),
                route('admin.accounting.movement.show', $event->document),
                'folder-sync'
            ));
        }
    }
}
