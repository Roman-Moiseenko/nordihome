<?php

namespace App\Modules\Order\Application\Actions\OrderItem;

use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveOrderItemUseCase
{
    public function __construct(
        private OrderRepositoryInterface       $repository,
        private OrderCalculateService          $orderCalculateService,
    )
    {
    }
    public function execute(int $orderId, int $itemId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);

        $orderEntity->removeItem($itemId);

        $orderEntity = $this->repository->save($orderEntity);
        $this->orderCalculateService->execute($orderEntity->id);
    }
}
