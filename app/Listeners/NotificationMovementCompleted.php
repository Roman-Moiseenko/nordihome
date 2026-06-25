<?php

namespace App\Listeners;

use App\Events\MovementHasCompleted;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

class NotificationMovementCompleted
{
    public function __construct(private ListStaffByPositionUseCase $positionUseCase)

    {}

    /**
     * Handle the event.
     */
    public function handle(MovementHasCompleted $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Поступило перемещение',
                $event->document->htmlNumDate(),
                route('admin.accounting.movement.show', $event->document),
                'folder-sync'
            ));
        }
*/
    }
}
