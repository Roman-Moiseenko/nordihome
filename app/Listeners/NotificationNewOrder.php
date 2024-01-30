<?php

namespace App\Listeners;

use App\Entity\Admin;
use App\Events\OrderHasCreated;
use App\Mail\OrderNew;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationNewOrder
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasCreated $event): void
    {
        //Письмо клиенту о новом заказе
        Mail::to($event->order->user->email)->queue(new OrderNew($event->order));


        //TODO Уведомление на телеграм Админу или Товароведу??
        $staffs = Admin::where('role', Admin::ROLE_COMMODITY)->get();
        $message = "Новый заказ\nТип " . $event->order->getType();
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage($message));
        }
    }
}
