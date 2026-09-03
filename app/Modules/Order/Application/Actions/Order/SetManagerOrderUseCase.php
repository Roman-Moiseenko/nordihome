<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Auth\Application\Actions\Staff\ViewStaffUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SetManagerOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface    $repository,
        private OrderLoggerServiceInterface $logger,
        private ViewStaffUseCase $staffUseCase,
        private SetStatusOrderUseCase $setStatusOrderUseCase,
    )
    {
    }

    public function execute(int $orderId, $staffId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $orderEntity = $this->repository->getById($orderId);


        $orderEntity->staffId = $staffId;

        $this->repository->save($orderEntity);

        if ($orderEntity->status->value === OrderStatus::new()) {
            $dto = new StatusOrderAssignData($orderId, OrderStatus::draft());

            $this->setStatusOrderUseCase->execute($dto);
        }

        //Нужно ФИО менеджера для логирования
        $staffEntity = $this->staffUseCase->execute($staffId, new UserPermission(permissions: ['auth.employee.view']));
        $this->logger->log(orderId: $orderEntity->id, action: 'Назначен менеджер',
            value: $staffEntity->fullName);
    }
}
