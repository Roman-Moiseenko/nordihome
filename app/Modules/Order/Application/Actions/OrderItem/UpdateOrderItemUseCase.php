<?php

namespace App\Modules\Order\Application\Actions\OrderItem;

use App\Modules\Order\Application\Actions\Order\SetAssemblagesOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetPackingsOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemUpdateData;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateOrderItemUseCase
{
    public function __construct(
        private OrderRepositoryInterface   $repository,
        private OrderCalculateService      $orderCalculateService,
        private SetAssemblagesOrderUseCase $setAssemblagesOrderUseCase,
        private SetPackingsOrderUseCase    $setPackingsOrderUseCase,
        private CreateOrderLoggerUseCase $loggerUseCase,
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

        //Логирование операций
        $this->logger($dto, $orderEntity);
    }

    private function logger(OrderItemUpdateData $dto, OrderEntity $orderEntity): void
    {
        $item = $orderEntity->getItem($dto->id);
        $action = null;
        $value = '';

        if (!is_null($dto->sellCost)) {
            $action = 'Изменена цена позиции';
            $value = $dto->sellCost;
        }
        if (!is_null($dto->percentDiscount)) {
            $action = 'Установлена скидка позиции';
            $value = $dto->percentDiscount;
        }

        if (!is_null($dto->quantity)) {
            $action = 'Изменено кол-во позиции';
            $value = $dto->quantity;
        }
        if (!is_null($dto->assemblage)) {
            $action = $dto->assemblage ? 'Назначена сборка позиции' : 'Отменена сборка позиции';
        }
        if (!is_null($dto->packing)) {
            $action = $dto->packing ? 'Назначена упаковка позиции' : 'Отменена упаковка позиции';
        }
        if (!is_null($dto->comment)) {
            $action = 'Изменен комментарий позиции';
            $value = $dto->comment;
        }


        if (is_null($action)) return;
        $log = new OrderLoggerCreateData(
            action: $action,
            object: $item->productId,
            value: $value,
            link: route('admin.catalog.product.edit', $item->productId),
        );
        $this->loggerUseCase->execute($orderEntity->id, $log);
    }
}
