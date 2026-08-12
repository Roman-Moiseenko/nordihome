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

    public function __construct(
        private MailServiceInterface $mailService,
        private ViewOrderUseCase     $viewOrderUseCase,
    )

    {}

    public function handle(OrderHasCreated $event): void
    {
        $permissions = new UserPermission(null, [], ['auth.buyer.view']);
        $orderView = $this->viewOrderUseCase->execute($event->orderId, $permissions);

        if (is_null($orderView->client)) throw new \InvalidArgumentException("Заказу $orderView->id не присвои клиент, невозможно отправить email");

        $template = MailTemplateRegistry::get('order.new');
        $this->mailService->send(
            $template,
            ['order' => $orderView,],
            new Recipient(email: $orderView->client->email, clientId: $orderView->client->id)
        );
    }
}
