<?php

namespace App\Listeners;

use App\Events\OrderHasCreated;
use App\Mail\OrderNew;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Support\Facades\Mail;

class NotificationNewOrder
{
    private StaffRepository $repository;

    /**
     * Create the event listener.
     */
    public function __construct(StaffRepository $repository)
    {
        //
        $this->repository = $repository;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasCreated $event): void
    {
        //Письмо клиенту о новом заказе
        Mail::to($event->order->user->email)->queue(new OrderNew($event->order));

        $staffs = $this->repository->getStaffsByCode(Responsibility::MANAGER_ORDER);

        $message = "Новый заказ\nТип " . $event->order->getType();
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage($message));
        }
    }
}
