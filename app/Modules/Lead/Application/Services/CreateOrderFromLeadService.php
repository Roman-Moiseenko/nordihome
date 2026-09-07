<?php

namespace App\Modules\Lead\Application\Services;

use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\Actions\Order\CreateOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateOrderFromLeadService
{
    public function __construct(
        private LeadRepositoryInterface $leadRepository,
        private CreateOrderUseCase $createOrderUseCase,
        private SetStatusOrderUseCase $setStatusOrderUseCase,
        private CreateOrderLoggerUseCase $loggerUseCase,
        private TransactionManagerInterface $transactionManager,
    )
    {

    }

    public function execute(int $leadId, UserPermission $permission):? OrderEntity
    {
        if (!$permission->can('lead.lead.edit')) throw new AccessDeniedException();
        $this->transactionManager->execute(function () use ($leadId, $permission) {

            $leadEntity = $this->leadRepository->findById($leadId);
            //Создаем пустой заказ
            $orderEntity = $this->createOrderUseCase->execute($leadEntity->clientId, $leadEntity->staffId, $permission);
            //Сразу устанавливаем статус в работе
            $dto = new StatusOrderAssignData(
                orderId: $orderEntity->id,
                status: OrderStatus::inWork(),
                comment: 'Заказ из Лида',
            );
            $this->setStatusOrderUseCase->execute($dto);
            //Присваиваем заказ лиду
            $leadEntity->orderId = $orderEntity->id;
            $this->leadRepository->save($leadEntity);

            $log = new OrderLoggerCreateData(action: 'Заказ создан из лида');
            $this->loggerUseCase->execute($leadEntity->orderId, $log);

            return $orderEntity;
        });
        return null;
    }
}
