<?php

namespace App\Modules\Order\Application\Actions\OrderItem;

use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveOrderItemUseCase
{
    public function __construct(
        private OrderRepositoryInterface $repository,
        private OrderCalculateService    $orderCalculateService,
        private CreateOrderLoggerUseCase $loggerUseCase,
    )
    {
    }

    public function execute(int $orderId, int $itemId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);
        $item = $orderEntity->getItem($itemId);

        $orderEntity->removeItem($itemId);

        $orderEntity = $this->repository->save($orderEntity);
        $this->orderCalculateService->execute($orderEntity->id);

        $log = new OrderLoggerCreateData(
            action: 'Товар удален из заказ',
            object: $item->productId, //FixMe Артикул и название
            value: $item->quantity . ' шт.',
            link: route('admin.catalog.product.edit', $item->productId),
        );
        $this->loggerUseCase->execute($orderEntity->id, $log);
    }
}
