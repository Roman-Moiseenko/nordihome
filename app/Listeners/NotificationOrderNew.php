<?php

namespace App\Listeners;

use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Mail\Mailable\OrderNew;
use App\Modules\Notification\Helpers\NotificationHelper;
use App\Modules\Notification\Helpers\TelegramParams;
use App\Modules\Notification\Message\StaffMessage;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Events\OrderHasCreated;

//use App\Mail\OrderNew;

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

        SendSystemMail::dispatch($event->order->user, new OrderNew($event->order), Order::class, $event->order->id);
        $_items = '';
        foreach ($event->order->items as $item) {
            $_items .= "\n" . $item->product->name . ' ' . $item->quantity . " шт";
        }


        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);

        $params = new TelegramParams( TelegramParams::OPERATION_ORDER_TAKE, $event->order->id);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                NotificationHelper::EVENT_NEW_ORDER,
                "Список товаров " . $_items,
                '',
            $params,
            ));
        }
    }
}
