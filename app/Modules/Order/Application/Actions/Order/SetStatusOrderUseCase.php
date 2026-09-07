<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;


/**
 * Используется только в сервисах по установке статусов или в сервисах создания заказа (new())
 * атомарная операция
 */
readonly class SetStatusOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface    $repository,
    )
    {
    }

    public function execute(StatusOrderAssignData $dto): void
    {
        $orderEntity = $this->repository->getById($dto->orderId);
        $orderEntity->addStatus($dto->status, $dto->comment, $dto->numberDocument, $dto->dateDocument);
        $this->repository->save($orderEntity);
    }
}
