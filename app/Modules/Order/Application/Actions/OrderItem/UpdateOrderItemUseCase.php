<?php

namespace App\Modules\Order\Application\Actions\OrderItem;

use App\Modules\Order\Application\DTOs\OrderItem\OrderItemUpdateData;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

class UpdateOrderItemUseCase
{
    public function __construct(
        private OrderRepositoryInterface $repository,
        private OrderCalculateService $calculateService,
    )
    {

    }
    public function execute(int $orderId, OrderItemUpdateData $dto, UserPermission $permission)
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $orderEntity = $this->repository->getById($orderId);

        $orderEntity->updateItem($dto);

        $orderEntity = $this->repository->save($orderEntity);
        $this->calculateService->execute($orderEntity->id);

    }
}
