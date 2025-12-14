<?php

namespace App\Listeners;

use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Events\OrderHasRefund;
use App\Notifications\StaffMessage;

class NotificationRefundNew
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasRefund $event): void
    {
        $staffs = $this->staffs->getChief();
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Возврат по заказу',
                'Заказ ' . $event->order->htmlNumDate(),
                route('admin.order.show', $event->order),
                'refresh-ccw'
            ));
        }
    }
}
