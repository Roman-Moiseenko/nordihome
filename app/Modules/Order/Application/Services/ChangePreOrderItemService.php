<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Order\Application\Actions\OrderItem\AddProductOrderUseCase;
use App\Modules\Order\Application\Actions\OrderItem\RemoveOrderItemUseCase;
use App\Modules\Order\Application\Actions\OrderItem\UpdateOrderItemUseCase;
use App\Modules\Order\Application\DTOs\OrderAddProductData;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemPreData;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemUpdateData;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ChangePreOrderItemService
{
    public function __construct(
        private OrderRepositoryInterface    $repository,
        private AddProductOrderUseCase      $addProductOrderUseCase,
        private UpdateOrderItemUseCase      $updateOrderItemUseCase,
        private RemoveOrderItemUseCase      $removeOrderItemUseCase,
        private TransactionManagerInterface $transactionManager,
    )
    {
    }

    public function execute(int $orderId, OrderItemPreData $dto, UserPermission $permission)
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $this->transactionManager->execute(function () use ($orderId, $dto, $permission) {
            //1. Уменьшаем кол-во текущего
            $orderEntity = $this->repository->getById($orderId);
            $baseItem = $orderEntity->getItem($dto->id);

            $quantity = min($baseItem->quantity, $dto->quantity); //Ограничиваем по верху

            if ($baseItem->quantity == $quantity) {//Переносим все
                $this->removeOrderItemUseCase->execute($orderId, $dto->id, $permission);
            } else {//Изменяем кол-во
                $updateDto = new OrderItemUpdateData(
                    id: $dto->id,
                    quantity: $baseItem->quantity - $quantity,
                );
                $this->updateOrderItemUseCase->execute($orderId, $updateDto, $permission);
            }
            //2. Добавляем товар, с увеличением кол-ва
            $addProductDto = new OrderAddProductData(
                productId: $baseItem->productId,
                quantity: $dto->quantity,
                preorder: $dto->preorder,
                increase: true,
            );
            $this->addProductOrderUseCase->execute($orderId, $addProductDto, $permission); //Сохранит и пересчитает
        });
    }
}
