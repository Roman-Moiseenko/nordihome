<?php

namespace App\Modules\Order\Application\Actions\OrderAddition;

use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\OrderAddition\OrderAdditionUpdateData;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemUpdateData;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateOrderAdditionUseCase
{
    public function __construct(
        private OrderRepositoryInterface $repository,
        private OrderCalculateService    $orderCalculateService,
        private CreateOrderLoggerUseCase $loggerUseCase,
    )
    {
    }

    public function execute(int $orderId, OrderAdditionUpdateData $dto, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);

        $orderEntity->updateAddition($dto);

        $orderEntity = $this->repository->save($orderEntity);
        $orderEntity = $this->orderCalculateService->execute($orderEntity->id);
        $this->logger($dto, $orderEntity);
    }


    private function logger(OrderAdditionUpdateData $dto, OrderEntity $orderEntity): void
    {
        $addition = $orderEntity->getAddition($dto->id);
        $action = null;
        $value = '';

        if (!is_null($dto->amount)) {
            $action = 'Изменена сумма услуги';
            $value = $dto->amount;
        }

        if (!is_null($dto->quantity)) {
            $action = 'Изменено кол-во услуги';
            $value = $dto->quantity;
        }

        if (!is_null($dto->comment)) {
            $action = 'Изменен комментарий услуги';
            $value = $dto->comment;
        }

        if (is_null($action)) return;
        $log = new OrderLoggerCreateData(
            action: $action,
            object: $addition->additionId, //FixMe Артикул и название
            value: $value,
        );
        $this->loggerUseCase->execute($orderEntity->id, $log);
    }
}
