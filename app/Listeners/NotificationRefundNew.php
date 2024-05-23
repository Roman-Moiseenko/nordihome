<?php

namespace App\Listeners;

use App\Events\OrderHasRefund;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
                route('admin.sales.order.show', $event->order),
                'refresh-ccw'
            ));
        }
    }
}
