<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Order\Application\Actions\Order\SendMailNewOrderClientUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
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
    ){}

    public function execute(int $orderId, array|null $emails, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $this->transactionManager->execute(function () use ($orderId, $emails, $permission) {
            //1. Меняем статус
            $this->statusOrderUseCase->execute($orderId, OrderStatus::awaiting());

            //2. Отправка Счета клиенту
            $this->sendMailNewOrderClientUseCase->execute($orderId, $emails);

            //TODO Отправка Заказа в 1С

            //TODO Уведомления ??

        });
    }


}
