<?php

namespace App\Modules\Order\Application\Actions\OrderAddition;

use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveOrderAdditionUseCase
{

    public function __construct(
        private OrderRepositoryInterface $repository,
        private OrderCalculateService    $orderCalculateService,
        private CreateOrderLoggerUseCase $loggerUseCase,
    )
    {
    }

    public function execute(int $orderId, int $additionId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);

        $addition = $orderEntity->getAddition($additionId);
        $orderEntity->removeAddition($additionId);

        $orderEntity = $this->repository->save($orderEntity);
        $this->orderCalculateService->execute($orderEntity->id);

        $log = new OrderLoggerCreateData(
            action: 'Услуга удалена из заказ',
            object: $addition->additionId, //FixMe Артикул и название
            value: $addition->amount . ' руб.',
        );
        $this->loggerUseCase->execute($orderEntity->id, $log);
    }
}
