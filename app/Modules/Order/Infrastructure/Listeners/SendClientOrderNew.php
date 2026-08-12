<?php

namespace App\Modules\Order\Infrastructure\Listeners;

use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Mail\Entity\MailTemplateRegistry;
use App\Modules\Order\Application\Actions\GetOrderUseCase;
use App\Modules\Order\Application\Actions\ViewOrderUseCase;
use App\Modules\Order\Application\DTOs\OrderViewData;
use App\Modules\Order\Infrastructure\Events\OrderHasCreated;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use App\Modules\Shared\Domain\Entities\UserPermission;

//use App\Mail\OrderNew;

readonly class SendClientOrderNew
{

    /**
     * Create the event listener.
     */
    public function __construct(
        private MailServiceInterface $mailService,
        private ViewClientUseCase    $viewClientUseCase,
        private ViewOrderUseCase     $viewOrderUseCase,
    )

    {}

    /**
     * Handle the event.
     */
    public function handle(OrderHasCreated $event): void
    {
        $permissions = new UserPermission(null, [], ['auth.buyer.view']);
        $orderView = $this->viewOrderUseCase->execute($event->orderId, $permissions);
        $client = $this->viewClientUseCase->execute($orderView->clientId, $permissions);

        $template = MailTemplateRegistry::get('order.new');
        $this->mailService->send(
            $template,
            ['order' => $orderView,],
            new Recipient(email: $client->email->value, clientId: $client->id)
        );


        // 2. Отправить письмо (инфраструктура)
       // Mail::to($orderView->client->email)->send(new OrderConfirmationMail($orderView));

        //Письмо клиенту о новом заказе
    //    SendSystemMail::dispatch($event->order->client, new OrderNew($event->order), Order::class, $event->order->id);




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
