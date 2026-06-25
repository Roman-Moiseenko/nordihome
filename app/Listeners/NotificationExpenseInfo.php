<?php

namespace App\Listeners;

use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Order\Events\ExpenseHasAssembling;

class NotificationExpenseInfo
{
    public function __construct(private ListStaffByPositionUseCase $positionUseCase)

    {}


    public function handle(ExpenseHasAssembling $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());


        //FIXME Модуль Notification - через RecipientResolverInterface
/*
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                event: NotificationHelper::EVENT_INFO,
                message: 'Новое распоряжение на сборку',

            ));
        }
*/
    }
}
