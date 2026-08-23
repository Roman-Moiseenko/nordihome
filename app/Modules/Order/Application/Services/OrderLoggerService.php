<?php
declare(strict_types=1);

namespace App\Modules\Order\Application\Services;

use App\Modules\Auth\Infrastructure\Models\Staff;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Infrastructure\Models\LoggerOrder;

class OrderLoggerService implements OrderLoggerServiceInterface
{
    public function log(int $orderId, string $action, $object = '', $value = '', $old = '', $link = null): void
    {

        if (!auth()->check()) return;
        /** @var Staff $staff */
        $staffId = auth()->user()->profileable_id;
        $logger = LoggerOrder::register($orderId, $staffId, $action, (string)$object, (string)$value, (string)$old, $link);
        if (empty($logger)) throw new \DomainException('Ошибка записи лога учета действий по Заказу');
    }
}
