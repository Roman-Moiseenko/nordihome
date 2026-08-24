<?php

namespace App\Modules\Order\Application\Actions\OrderItem;

use App\Modules\Order\Application\Actions\Order\SetAssemblagesOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetPackingsOrderUseCase;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemUpdateData;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateOrderItemUseCase
{
    public function __construct(
        private OrderRepositoryInterface   $repository,
        private OrderCalculateService      $orderCalculateService,
        private SetAssemblagesOrderUseCase $setAssemblagesOrderUseCase,
        private SetPackingsOrderUseCase    $setPackingsOrderUseCase,
    )
    {
    }

    public function execute(int $orderId, OrderItemUpdateData $dto, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $orderEntity = $this->repository->getById($orderId);
        $orderEntity->updateItem($dto);
        $orderEntity = $this->repository->save($orderEntity);
        $this->orderCalculateService->execute($orderEntity->id);

        //Услуга сборки
        if (!is_null($dto->assemblage)) {
            $this->setAssemblagesOrderUseCase->execute($orderId, $dto->assemblage, [$dto->id], $permission);

        }

        //Услуга упаковки
        if (!is_null($dto->packing)) {
            $this->setPackingsOrderUseCase->execute($orderId, $dto->packing, [$dto->id], $permission);
        }

    }
}
