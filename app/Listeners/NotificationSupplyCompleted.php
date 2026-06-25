<?php

namespace App\Listeners;

use App\Events\SupplyHasCompleted;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;


class NotificationSupplyCompleted
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(SupplyHasCompleted $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Поступление товара от поставщика',
                $event->supply->distributor->name,
                route('admin.accounting.supply.show', $event->supply),
                'folder-pen'
            ));
        }
*/
    }
}
