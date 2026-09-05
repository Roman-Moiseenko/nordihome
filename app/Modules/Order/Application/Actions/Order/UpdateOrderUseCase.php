<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Order\Application\DTOs\Order\OrderUpdateData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $repository
    )
    {
    }

    public function execute(int $orderId, OrderUpdateData $dto, UserPermission $permission): OrderEntity
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);


        if (!is_null($dto->createdAt)) $orderEntity->createdAt = $dto->createdAt;
        if (!is_null($dto->comment)) $orderEntity->comment = $dto->comment;
        if (!is_null($dto->traderId)) $orderEntity->traderId = $dto->traderId;
        if (!is_null($dto->shopperId)) $orderEntity->shopperId = $dto->shopperId;

        return $this->repository->save($orderEntity);
    }
}
