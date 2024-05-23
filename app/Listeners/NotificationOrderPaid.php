<?php

namespace App\Listeners;

use App\Events\OrderHasPaid;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationOrderPaid
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasPaid $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);

        foreach ($staffs as $staff) {
            if ($event->order->manager_id == $staff->id) {
                $staff->notify(new StaffMessage(
                    'Заказ оплачен',
                    'Заказ ' . $event->order->htmlNumDate(),
                    route('admin.sales.order.show', $event->order),
                    'credit-card'
                ));
            } }

        //TODO Уведомляем клиента, что его заказ полностью оплачен
    }
}
