<?php

namespace App\Listeners;

use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Order\Events\OrderHasRefund;


class NotificationRefundNew
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(OrderHasRefund $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::supervisor());
                //FIXME Модуль Notification - через RecipientResolverInterface
      /*
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Возврат по заказу',
                'Заказ ' . $event->order->htmlNumDate(),
                route('admin.order.show', $event->order),
                'refresh-ccw'
            ));
        }
      */
    }
}
