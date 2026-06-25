<?php
declare(strict_types=1);

namespace App\Modules\Analytics;

use App\Modules\Analytics\Entity\LoggerOrder;
use App\Modules\Auth\Infrastructure\Models\Staff;
use App\Modules\Order\Entity\Order\Order;

class LoggerService
{
    public function logOrder(Order $order, string $action, $object = '', $value = '', $old = '', $link = null): void
    {

        if (!auth()->check()) return;
        /** @var Staff $staff */
        $staff = auth()->user()->profileable;
        $logger = LoggerOrder::register($order->id, $staff->id, $action, (string)$object, (string)$value, (string)$old, $link);
        if (empty($logger)) throw new \DomainException('Ошибка записи лога учета действий по Заказу');
    }
}
