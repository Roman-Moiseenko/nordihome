<?php

namespace App\Listeners;

use App\Events\PaymentHasPaid;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

class NotificationPaymentNew
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(PaymentHasPaid $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*
        foreach ($staffs as $staff) {
            if ($event->payment->order->staff_id == $staff->id) {
                $staff->notify(new StaffMessage(
                    'Поступила оплата по заказу',
                    $event->payment->order->htmlNumDate(),
                    route('admin.order.show', $event->payment->order),
                    'credit-card'
                ));
            }
        }
        Mail::to($event->payment->order->user->email)->queue(new PaymentPaid($event->payment));
*/
    }
}
