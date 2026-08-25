<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Mail\Entity\MailTemplateRegistry;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class SendMailNewOrderClientUseCase
{
    public function __construct(
        private MailServiceInterface       $mailService,
        private ViewClientUseCase $clientUseCase,
        private OrderRepositoryInterface    $repository,
    ){}
    public function execute(int $orderId, array|null $emails): void
    {
        //TODO Возможно. Для Order сделать поле array $emails, при создании добавляем $client->email
        // В админке можно добавлять адреса

        $template = MailTemplateRegistry::get('order.new');
        $orderEntity = $this->repository->getById($orderId);

        //Из фронта нет данных о других email, берем от клиента
        if (empty($emails)) {
            $client = $this->clientUseCase->execute(
                $orderEntity->id,
                new UserPermission(permissions: ['auth.buyer.view'])
            );
            $emails[] = $client->email->value;
        }

        foreach ($emails as $email) {
            $this->mailService->send($template,
                ['order' => $orderEntity,],
                new Recipient(email: $email, clientId: $orderEntity->id)
            );
        }
    }
}
