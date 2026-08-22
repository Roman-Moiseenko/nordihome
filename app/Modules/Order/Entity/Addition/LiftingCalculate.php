<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;

use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Infrastructure\Models\Order;

class LiftingCalculate extends CalculateAddition
{
    /**
     * @param Order $order
     * @param int $base - стоимость поднятия 1 кг за этаж
     * @return int
     */
    public static function calculate(Order $order, int $base): int
    {
        $result = 0;
        foreach ($order->items as $item) {
            $result += $item->product->weight() * $item->quantity;
        }
        if ($result < 1) $result = 1;
        return (int)ceil($result * $base);
    }

    public static function calculateEntity(OrderEntity $order, int $base): int
    {
        $repository = app()->make(ProductRepositoryInterface::class);
        $result = 0;

        foreach ($order->items as $item) {
          $productEntity = $repository->find($item->productId);
            $result += $productEntity->weight() * $item->quantity;

        }
        if ($result < 1) $result = 1;
        return (int)ceil($result * $base);
    }
}
