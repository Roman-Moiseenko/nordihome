<?php

namespace App\Listeners;

use App\Events\PaymentHasPaid;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationPaymentNew
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentHasPaid $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);
        foreach ($staffs as $staff) {
            if ($event->payment->order->manager_id == $staff->id) {
                $staff->notify(new StaffMessage(
                    'Поступила оплата по заказу',
                    $event->payment->order->htmlNumDate(),
                    route('admin.sales.order.show', $event->payment->order),
                    'credit-card'
                ));
            }
        }
        //TODO Для автоматических платежей - Уведомляем клиента, что платеж получен
    }
}
