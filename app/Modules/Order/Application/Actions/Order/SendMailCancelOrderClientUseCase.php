<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Mail\Entity\MailTemplateRegistry;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class SendMailCancelOrderClientUseCase
{
    public function __construct(
        private MailServiceInterface     $mailService,
        private ViewClientUseCase        $clientUseCase,
        private OrderRepositoryInterface $repository,
    )
    {
    }

    public function execute(int $orderId): void
    {
        $template = MailTemplateRegistry::get('order.cancelled');
        $orderEntity = $this->repository->getById($orderId);

        $client = $this->clientUseCase->execute(
            $orderEntity->id,
            new UserPermission(permissions: ['auth.buyer.view'])
        );

        $this->mailService->send($template,
            [
                'order' => $orderEntity,
                'comment' => $orderEntity->status->comment
            ],
            new Recipient(email: $client->email->value, clientId: $orderEntity->id)
        );

    }
}
