<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Mail\Entity\MailTemplateRegistry;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;

readonly class SendMailCancelOrderClientUseCase
{
    public function __construct(
        private MailServiceInterface     $mailService,
        private OrderRepositoryInterface $repository,
        private ClientRepositoryInterface $clientRepository,
    )
    {
    }

    public function execute(int $orderId): void
    {
        $orderEntity = $this->repository->getById($orderId);
        if (is_null($orderEntity->clientId)) return; //

        $client = $this->clientRepository->findById($orderEntity->clientId);

        $template = MailTemplateRegistry::get('order.cancelled');
        $this->mailService->send($template,
            [
                'order' => $orderEntity,
                'comment' => $orderEntity->status->comment
            ],
            new Recipient(email: $client->email->value, clientId: $orderEntity->clientId)
        );

    }
}
