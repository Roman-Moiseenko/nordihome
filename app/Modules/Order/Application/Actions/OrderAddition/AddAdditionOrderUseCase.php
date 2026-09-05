<?php

namespace App\Modules\Order\Application\Actions\OrderAddition;

use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class AddAdditionOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $repository,
        private OrderCalculateService    $orderCalculateService,
    )
    {
    }

    public function execute(int $orderId, int $additionId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);

        $orderEntity->addAddition($additionId);

        $orderEntity = $this->repository->save($orderEntity);
        $this->orderCalculateService->execute($orderEntity->id);
    }
}
