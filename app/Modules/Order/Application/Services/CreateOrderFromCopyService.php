<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateOrderFromCopyService
{

    public function __construct(
        private OrderRepositoryInterface $repository,
        private OrderLoggerServiceInterface $logger,

    )
    {
    }

    public function execute(int $orderId, UserPermission $permission): OrderEntity
    {
        if (!$permission->can('order.order.create')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);
        $orderEntity->id = null;
        $orderEntity->number = null;
        $orderEntity = $this->repository->save($orderEntity);

        $this->logger->log(orderId: $orderEntity->id, action: 'Создан заказ копированием');
        return $orderEntity;
    }
}
