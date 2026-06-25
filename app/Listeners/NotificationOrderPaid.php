<?php

namespace App\Listeners;

use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Order\Events\OrderHasPaid;

class NotificationOrderPaid
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}


    /**
     * Handle the event.
     */
    public function handle(OrderHasPaid $event): void
    {

        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());


        //FIXME Модуль Notification - через RecipientResolverInterface
/*
        foreach ($staffs as $staff) {
            if ($event->order->staff_id == $staff->id) {
                $staff->notify(new StaffMessage(
                    'Заказ оплачен',
                    'Заказ ' . $event->order->htmlNumDate(),
                    route('admin.order.show', $event->order),
                    'credit-card'
                ));
            }
        }

        Mail::to($event->order->user->email)->queue(new OrderPaid($event->order));
*/
    }
}
