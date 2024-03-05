<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Order\Entity\Order\Order;

class OrderRepository
{
    public function getNewOrders()
    {
        return Order::where('finished', false)->where('preorder', false)->orderByDesc('created_at');
    }

    public function getPreOrders()
    {
        return Order::where('finished', false)->where('preorder', true)->where('type', '<>', Order::PARSER)->orderByDesc('created_at');
    }

    public function getParser()
    {
        return Order::where('finished', false)->where('preorder', true)->where('type', Order::PARSER)->orderByDesc('created_at');
    }

    public function getExecuted()
    {
        return Order::where('finished', true)->orderByDesc('created_at');
    }
}
