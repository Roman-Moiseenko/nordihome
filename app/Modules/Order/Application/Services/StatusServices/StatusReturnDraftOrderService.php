<?php

namespace App\Modules\Order\Application\Services\StatusServices;

use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusReturnDraftOrderService
{
    public function __construct(
        private TransactionManagerInterface   $transactionManager,
        private SetStatusOrderUseCase         $statusOrderUseCase,
        private SetStatusLeadFromOrderUseCase $leadFromOrderUseCase,
    )
    {
    }

    public function execute(int $orderId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $this->transactionManager->execute(function () use ($orderId) {
            //1. Меняем статус
            $dto = new StatusOrderAssignData($orderId, OrderStatus::inWork());
            $this->statusOrderUseCase->execute($dto);

            $this->leadFromOrderUseCase->execute($dto->orderId, LeadStatusValue::IN_WORK);


            //TODO Отправка Отмены Заказа в 1С

            //TODO Уведомления ??

        });

        //MAINDO 2. Отправка Письма клиенту, что заказ вернули в работу
        //$this->sendMailReturnOrderClientUseCase->execute($orderId, $emails);


    }
}
