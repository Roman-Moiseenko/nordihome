<?php

namespace App\Modules\Order\Application\Interfaces;

interface OrderLoggerServiceInterface
{
    public function log(int $orderId, string $action, $object = '', $value = '', $old = '', $link = null): void;

}
