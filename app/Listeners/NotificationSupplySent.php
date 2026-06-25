<?php

namespace App\Listeners;

use App\Events\SupplyHasSent;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Notifications\StaffMessage;

class NotificationSupplySent
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(SupplyHasSent $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

                //FIXME Отправка сообщений
                /*
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Поступило распоряжение на сборку',
                $event->supply->htmlNumDate(),
                route('admin.accounting.supply.show', $event->supply),
                'folder-pen'
            ));
        }
                */
    }
}
