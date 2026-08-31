<?php

namespace App\Modules\Order\Application\Services\StatusServices;

use App\Modules\Order\Application\Actions\Order\SendMailNewOrderClientUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusReturnDraftOrderService
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,
        private SetStatusOrderUseCase $statusOrderUseCase,
    ){}

    public function execute(int $orderId, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $this->transactionManager->execute(function () use ($orderId, $permission) {
            //1. Меняем статус
            $this->statusOrderUseCase->execute($orderId, OrderStatus::draft());

            //MAINDO 2. Отправка Письма клиенту, что заказ вернули в работу
            //$this->sendMailReturnOrderClientUseCase->execute($orderId, $emails);

            //TODO Отправка Отмены Заказа в 1С

            //TODO Уведомления ??

        });
    }
}
