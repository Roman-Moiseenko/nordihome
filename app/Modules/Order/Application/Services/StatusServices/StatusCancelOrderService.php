<?php

namespace App\Modules\Order\Application\Services\StatusServices;

use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\Order\SendMailCancelOrderClientUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusCancelOrderService
{
    public function __construct(
        private SetStatusOrderUseCase            $statusOrderUseCase,
        private SendMailCancelOrderClientUseCase $mailCancelOrderClientUseCase,
        private SetStatusLeadFromOrderUseCase    $leadFromOrderUseCase,
        private CreateOrderLoggerUseCase         $loggerUseCase,
        private TransactionManagerInterface      $transactionManager,
    )
    {
    }

    public function execute(int $orderId, string $comment, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        //TODO Проверка на платежи, если есть ошибка - нельзя отменить, необходим возврат
        $this->transactionManager->execute(function () use ($orderId, $comment) {

            $dto = new StatusOrderAssignData(
                orderId: $orderId,
                status: OrderStatus::cancelled(),
                comment: $comment,
            );

            $this->statusOrderUseCase->execute($dto);

            $this->leadFromOrderUseCase->execute($dto->orderId, LeadStatusValue::CANCELLED);

            $log = new OrderLoggerCreateData(action: 'Заказ отменен', value: $comment);
            $this->loggerUseCase->execute($orderId, $log);
        });
        $this->mailCancelOrderClientUseCase->execute($orderId);
    }
}
