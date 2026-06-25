<?php

namespace App\Listeners;

use App\Events\ReserveHasTimeOut;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

class NotificationReserveTimeOut
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(ReserveHasTimeOut $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*
        foreach ($staffs as $staff) {
            if ($staff->id == $event->order->staff_id) {
                $staff->notify(new StaffMessage(
                    'Резерв по товару',
                    ($event->timeOut ? 'Закончилось время резерва' : 'Закачивается время резерва, осталось менее 12 ч.') . ' ' . $event->order->htmlNumDate(),
                    route('admin.order.show', $event->order),
                    'baggage-claim'
                ));
            }
        }

        if ($event->order->isAwaiting()) {
            Mail::to($event->order->user->email)->queue(new OrderReserveOut($event->order, $event->timeOut));
        }
*/
    }
}
