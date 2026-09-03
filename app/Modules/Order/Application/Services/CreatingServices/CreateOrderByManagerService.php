<?php

namespace App\Modules\Order\Application\Services\CreatingServices;

use App\Modules\Order\Application\Actions\Order\CreateOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
use Illuminate\Events\Dispatcher;

readonly class CreateOrderByManagerService
{

    public function __construct(
        private OrderLoggerServiceInterface $logger,
        private SetStatusOrderUseCase $setStatusOrderUseCase,
        private Dispatcher                  $dispatcher,
        private CreateOrderUseCase $createOrderUseCase,
    )
    {
    }

    public function execute(int|null $clientId, int $staffId, UserPermission $permission): OrderEntity
    {
        if (!$permission->can('order.order.create')) throw new AccessDeniedException();
        //1. Создаем Заказ
        $orderEntity = $this->createOrderUseCase->execute($clientId, $staffId, $permission);
        //2. Записываем в лог
        $this->logger->log(orderId: $orderEntity->id, action: 'Заказ создан менеджером');
        //3. Создаем Лид (т.к. у заказа есть менеджер, лид присвоится ему)
        $leadData = new LeadSourceData(
            id: $orderEntity->id,
            able: 'order.order',
            data: [],
            orderId: $orderEntity->id,
        );
        $this->dispatcher->dispatch(new LeadCollected($leadData));
        //4. Меняем статус на в работе
        $dto = new StatusOrderAssignData($orderEntity->id, OrderStatus::draft());
        $this->setStatusOrderUseCase->execute($dto);

        return $orderEntity;
    }

}
