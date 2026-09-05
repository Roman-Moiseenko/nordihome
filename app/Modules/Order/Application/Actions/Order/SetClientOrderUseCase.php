<?php

namespace App\Modules\Order\Application\Actions\Order;


use App\Modules\Order\Application\DTOs\Order\AssignClientToOrderData;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;

/**
 * Не используется в одиночку, только через Сервис
 */
readonly class SetClientOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    )
    {
    }

    public function execute(int $orderId, int $clientId): void
    {
        $orderEntity = $this->repository->getById($orderId);
        $orderEntity->clientId = $clientId;
        $this->repository->save($orderEntity);
    }
}
