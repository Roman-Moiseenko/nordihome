<?php
declare(strict_types=1);

namespace App\Modules\Analytics;

use App\Modules\Analytics\Entity\LoggerOrder;
use App\Modules\Auth\Infrastructure\Models\Staff;
use App\Modules\Order\Infrastructure\Models\Order;

class LoggerService
{
    public function logOrder(int $orderId, string $action, $object = '', $value = '', $old = '', $link = null): void
    {

        if (!auth()->check()) return;
        /** @var Staff $staff */
        $staffId = auth()->user()->profileable_id;
        $logger = LoggerOrder::register($orderId, $staffId, $action, (string)$object, (string)$value, (string)$old, $link);
        if (empty($logger)) throw new \DomainException('Ошибка записи лога учета действий по Заказу');
    }
}
