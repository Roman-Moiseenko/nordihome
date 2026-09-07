<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;


/**
 * атомарная операция, сама не используется, только через сервисы
 */
readonly class SetManagerOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface    $repository,
    )
    {
    }

    public function execute(int $orderId, ?int $staffId): void
    {
        $orderEntity = $this->repository->getById($orderId);
        $orderEntity->staffId = $staffId;
        $this->repository->save($orderEntity);

    }
}
