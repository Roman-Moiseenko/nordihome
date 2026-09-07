<?php

namespace App\Modules\Order\Application\Services\CreatingServices;

use App\Modules\Lead\Application\Actions\SetManagerLeadFromOrderUseCase;
use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
use Illuminate\Events\Dispatcher;

readonly class CreateOrderFromCopyService
{

    public function __construct(
        private OrderRepositoryInterface       $repository,
        private SetStatusOrderUseCase          $setStatusOrderUseCase,
        private Dispatcher                     $dispatcher,
        private TransactionManagerInterface    $transactionManager,
        private SetStatusLeadFromOrderUseCase  $leadFromOrderUseCase,
        private SetManagerLeadFromOrderUseCase $setManagerLeadUseCase,
        private CreateOrderLoggerUseCase $loggerUseCase,

    )
    {
    }

    public function execute(int $orderId, int $staffId, UserPermission $permission):? OrderEntity
    {
        if (!$permission->can('order.order.create')) throw new AccessDeniedException();
        $this->transactionManager->execute(function () use ($orderId, $staffId) {

            $orderEntity = $this->repository->getById($orderId);

            $orderEntity->id = null;
            $orderEntity->number = null;
            $orderEntity->statuses = [];
            $orderEntity->addStatus(OrderStatus::new());
            $orderEntity->staffId = $staffId; //Ставим себя менеджером

            $orderEntity = $this->repository->save($orderEntity);

            $leadData = new LeadSourceData(
                id: $orderEntity->id,
                able: 'order.order',
                data: [],
                orderId: $orderEntity->id,
            );
            $this->dispatcher->dispatch(new LeadCollected($leadData));

            $dto = new StatusOrderAssignData($orderId, OrderStatus::inWork());
            $this->setStatusOrderUseCase->execute($dto);

            $this->leadFromOrderUseCase->execute($orderId, LeadStatusValue::IN_WORK);
            $this->setManagerLeadUseCase->execute($orderId, $staffId);

            $log = new OrderLoggerCreateData(action: 'Заказ создан копированием', link: route('admin.order.show', $orderId));
            $this->loggerUseCase->execute($orderEntity->id, $log);

            return $orderEntity;
        });

        return null;
    }
}
