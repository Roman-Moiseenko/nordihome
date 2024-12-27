<?php

namespace App\Listeners;

use App\Events\OrderHasPrepaid;
use App\Mail\OrderPrepaid;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationOrderPrepaid
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasPrepaid $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);

        foreach ($staffs as $staff) {
            if ($event->order->staff_id == $staff->id) {
                $staff->notify(new StaffMessage(
                    'Внесена предоплата по заказу',
                    'Заказ ' . $event->order->htmlNumDate(),
                    route('admin.order.show', $event->order),
                    'credit-card'
                ));
            }
        }

        Mail::to($event->order->user->email)->queue(new OrderPrepaid($event->order));

    }
}
