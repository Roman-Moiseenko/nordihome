<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Notification\Events\TelegramHasReceived;
use App\Modules\Notification\Helpers\TelegramParams;
use App\Modules\Order\Application\Services\StatusInWorkOrderService;
use App\Modules\Shared\Domain\Entities\UserPermission;



readonly class OrderService
{


    public function __construct(
        private StatusInWorkOrderService $statusInWorkOrderService,

    )
    {
    }


    /** Обрабатываем подтверждения из Телеграм */
    //FIXME вынести в отдельный сервис и его подписать на события телеграм?
    public function handle(TelegramHasReceived $event): void
    {
        if ($event->operation == TelegramParams::OPERATION_ORDER_TAKE) {
            try {
                $this->statusInWorkOrderService->execute($event->id, $event->staff->id, new UserPermission(null, [] , ['order.order.edit']));
                //FIXME Отправка сообщений
                /*
                $event->staff->notify(
                    new StaffMessage(
                        NotificationHelper::EVENT_INFO,
                        'Принято!'
                    )
                );
                */
            } catch (\DomainException $e) {
                                //FIXME Отправка сообщений
                /*
                $event->staff->notify(
                    new StaffMessage(
                        NotificationHelper::EVENT_ERROR,
                        $e->getMessage()
                    )
                );
                */
            }

        }
    }

}
