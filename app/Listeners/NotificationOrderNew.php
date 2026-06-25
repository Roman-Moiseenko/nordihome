<?php

namespace App\Listeners;

use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Mail\Mailable\OrderNew;
use App\Modules\Notification\Helpers\TelegramParams;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Events\OrderHasCreated;

//use App\Mail\OrderNew;

readonly class NotificationOrderNew
{

    /**
     * Create the event listener.
     */
    public function __construct(private ListStaffByPositionUseCase $positionUseCase)

    {}

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
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        $params = new TelegramParams( TelegramParams::OPERATION_ORDER_TAKE, $event->order->id);


        //FIXME Модуль Notification - через RecipientResolverInterface
      /*  foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                NotificationHelper::EVENT_NEW_ORDER,
                "Список товаров " . $_items,
                '',
            $params,
            ));
        }
      */
    }
}
