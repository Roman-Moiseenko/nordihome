<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SetClientOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $repository
    )
    {
    }

    public function execute(int $orderId, int $clientId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);
        $orderEntity->clientId = $clientId;
        $this->repository->save($orderEntity);
    }
}
