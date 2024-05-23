<?php

namespace App\Listeners;

use App\Events\OrderHasCreated;
use App\Mail\OrderNew;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Support\Facades\Mail;

class NotificationOrderNew
{
    private StaffRepository $staffs;

    /**
     * Create the event listener.
     */
    public function __construct(StaffRepository $repository)
    {
        $this->staffs = $repository;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasCreated $event): void
    {
        //Письмо клиенту о новом заказе
        Mail::to($event->order->user->email)->queue(new OrderNew($event->order));

        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Новый заказ',
                $event->order->getType(),
                route('admin.sales.order.show', $event->order),
                'file-plus-2'
            ));
        }
    }
}
