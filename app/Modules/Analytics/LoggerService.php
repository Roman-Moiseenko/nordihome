<?php
declare(strict_types=1);

namespace App\Modules\Analytics;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Analytics\Entity\LoggerOrder;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Support\Facades\Auth;

class LoggerService
{
    public function logOrder(Order $order, string $action, $object, $value)
    {
        if (!Auth::guard('admin')->check()) return;
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $logger = LoggerOrder::register($order->id, $staff->id, $action, $object, $value);
        if (empty($logger)) throw new \DomainException('Ошибка записи лога учета действий по Заказу');
    }
}
