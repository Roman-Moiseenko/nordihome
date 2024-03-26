<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

class OrderRepository
{
    #[Deprecated]
    public function getNewOrders()
    {
        return Order::where('finished', false)->where('preorder', false)->orderByDesc('created_at');
    }

    #[Deprecated]
    public function getPreOrders()
    {
        return Order::where('finished', false)->where('preorder', true)->where('type', '<>', Order::PARSER)->orderByDesc('created_at');
    }

    #[Deprecated]
    public function getParser()
    {
        return Order::where('finished', false)->where('preorder', true)->where('type', Order::PARSER)->orderByDesc('created_at');
    }

    #[Deprecated]
    public function getExecuted()
    {
        return Order::where('finished', true)->orderByDesc('created_at');
    }

    public function getOrders(string $filter)
    {
        $query = Order::orderByDesc('created_at');
        //$filter = $request['filter'];
        if ($filter == 'all') return $query;

        if ($filter == 'new')
            $query->whereHas('status', function ($q) {
                $q->where('value', '<', OrderStatus::AWAITING);
            });
        if ($filter == 'awaiting')
            $query->whereHas('status', function ($q) {
                $q->where('value', OrderStatus::AWAITING);
            });

        if ($filter == 'at-work')
            $query->whereHas('status', function ($q) {
                $q->where('value', '>' , OrderStatus::AWAITING)->where('value', '<', OrderStatus::CANCEL);
            });

        if ($filter == 'canceled')
            $query->whereHas('status', function ($q) {
                $q->where('value', '>=' , OrderStatus::CANCEL)->where('value', '<', OrderStatus::COMPLETED);
            });
        if ($filter == 'completed')
            $query->whereHas('status', function ($q) {
                $q->where('value', OrderStatus::COMPLETED);
            });
        return $query;
    }

    #[ArrayShape(['new' => "int", 'awaiting' => "int", 'at-work' => "int"])]
    public function getFilterCount(): array
    {
        return [
            'new' => Order::whereHas('status', function ($q) {
                $q->where('value', '<', OrderStatus::AWAITING);
            })->count(),
            'awaiting' => Order::whereHas('status', function ($q) {
                $q->where('value', OrderStatus::AWAITING);
            })->count(),
            'at-work' => Order::whereHas('status', function ($q) {
                $q->where('value', '>' , OrderStatus::AWAITING)->where('value', '<', OrderStatus::CANCEL);
            })->count(),
        ];
    }
}
