<?php

namespace App\Listeners;

use App\Events\MovementHasCompleted;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationMovementCompleted
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(MovementHasCompleted $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Поступило перемещение',
                $event->document->htmlNumDate(),
                route('admin.accounting.movement.show', $event->document),
                'folder-sync'
            ));
        }
    }
}
