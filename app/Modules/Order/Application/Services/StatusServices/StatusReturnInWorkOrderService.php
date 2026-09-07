<?php

namespace App\Modules\Order\Application\Services\StatusServices;

use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusReturnInWorkOrderService
{
    public function __construct(
        private TransactionManagerInterface   $transactionManager,
        private SetStatusOrderUseCase         $statusOrderUseCase,
        private SetStatusLeadFromOrderUseCase $leadFromOrderUseCase,
        private OrderRepositoryInterface $repository,
        private CreateOrderLoggerUseCase $loggerUseCase,
    )
    {
    }

    public function execute(int $orderId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $this->transactionManager->execute(function () use ($orderId) {
            $orderEntity = $this->repository->getById($orderId);
            if ($orderEntity->status->value->getValue() != OrderStatus::AWAITING)
                throw new \DomainException('Заказ нельзя вернуть в работу');
            //1. Меняем статус
            $dto = new StatusOrderAssignData($orderId, OrderStatus::inWork());
            $this->statusOrderUseCase->execute($dto);

            $this->leadFromOrderUseCase->execute($dto->orderId, LeadStatusValue::IN_WORK);

            $log = new OrderLoggerCreateData(action: 'Заказ возвращен в работу');
            $this->loggerUseCase->execute($orderEntity->id, $log);
            //TODO Отправка Отмены Заказа в 1С

            //TODO Уведомления ??

        });

        //MAINDO 2. Отправка Письма клиенту, что заказ вернули в работу
        //$this->sendMailReturnOrderClientUseCase->execute($orderId, $emails);


    }
}
