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
    )
    {
    }

    public function execute(int $orderId, ?int $staffId): void
    {
        $orderEntity = $this->repository->getById($orderId);
        $orderEntity->staffId = $staffId;
        $this->repository->save($orderEntity);

        //Нужно ФИО менеджера для логирования
        if (!is_null($staffId)) {
            $staffEntity = $this->staffUseCase->execute($staffId, new UserPermission(permissions: ['auth.employee.view']));
            $this->logger->log(orderId: $orderEntity->id, action: 'Назначен менеджер',
                value: $staffEntity->fullName);
        } else {
            $this->logger->log(orderId: $orderEntity->id, action: 'Менеджер сброшен',);
        }
    }
}
