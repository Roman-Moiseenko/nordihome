<?php

namespace App\Modules\Order\Application\Services\StatusServices;

use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\Order\SendMailNewOrderClientUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusAwaitingOrderService
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,
        private SendMailNewOrderClientUseCase $sendMailNewOrderClientUseCase,
        private SetStatusOrderUseCase $statusOrderUseCase,
        private SetStatusLeadFromOrderUseCase $leadFromOrderUseCase,
        private OrderRepositoryInterface $repository,
        private CreateOrderLoggerUseCase $loggerUseCase,
    ){}

    public function execute(int $orderId, array|null $emails, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $this->transactionManager->execute(function () use ($orderId, $emails) {

            $orderEntity = $this->repository->getById($orderId);
            if ($orderEntity->getTotalAmount() == 0) throw new \DomainException('Сумма заказа не может быть равно нулю');
            if ($orderEntity->status->value->getValue() != OrderStatus::IN_WORK)
                throw new \DomainException('Нельзя отправить заказ на оплату. Не верный статус');

            //1. Меняем статус
            $dto = new StatusOrderAssignData($orderId, OrderStatus::awaiting());

            $this->statusOrderUseCase->execute($dto);

            $this->leadFromOrderUseCase->execute($dto->orderId, LeadStatusValue::INVOICE);

            $log = new OrderLoggerCreateData(action: 'Заказ отправлен на оплату');
            $this->loggerUseCase->execute($orderEntity->id, $log);

            //TODO Отправка Заказа в 1С

            //TODO Уведомления ??

        });


        //2. Отправка Счета клиенту
        $this->sendMailNewOrderClientUseCase->execute($orderId, $emails);
    }


}
