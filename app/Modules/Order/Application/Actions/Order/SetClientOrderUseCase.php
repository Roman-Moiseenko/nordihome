<?php

namespace App\Modules\Order\Application\Actions\Order;


use App\Modules\Order\Application\DTOs\Order\AssignClientToOrderData;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;

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

    public function execute(AssignClientToOrderData $dto): void
    {
        $orderEntity = $this->repository->getById($dto->orderId);
        $orderEntity->clientId = $dto->clientId;
        $this->repository->save($orderEntity);
    }
}
