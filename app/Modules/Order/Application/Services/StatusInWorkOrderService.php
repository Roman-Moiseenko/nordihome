<?php

namespace App\Modules\Order\Application\Services;


use App\Modules\Lead\Application\Actions\SetManagerLeadUseCase;
use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\Order\SetManagerOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusInWorkOrderService
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,
        private SetManagerOrderUseCase $setManagerOrderUseCase,
        private SetStatusLeadFromOrderUseCase $leadFromOrderUseCase,
        private SetStatusOrderUseCase $setStatusOrderUseCase,
        private SetManagerLeadUseCase $setManagerLeadUseCase,
    )
    {
    }

    public function execute(int $orderId, int $staffId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $this->transactionManager->execute(function () use ($orderId, $staffId) {
            //TODO Возможно проверка на наличие клиента
            $this->setManagerOrderUseCase->execute($orderId, $staffId);

            $dto = new StatusOrderAssignData($orderId, OrderStatus::inWork());
            $this->setStatusOrderUseCase->execute($dto);

            $this->leadFromOrderUseCase->execute($orderId, LeadStatusValue::IN_WORK);

            $this->setManagerLeadUseCase->execute($orderId, $staffId);
        });

    }

}
