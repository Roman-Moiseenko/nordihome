<?php

namespace App\Modules\Order\Domain\Interfaces;

use App\Modules\Order\Domain\Entities\OrderLoggerEntity;

interface OrderLoggerRepositoryInterface
{
    public function save(OrderLoggerEntity $entity): OrderLoggerEntity;

    /**
     * @param int $orderId
     * @return OrderLoggerEntity[]
     */
    public function getByOrderId(int $orderId): array;
}
