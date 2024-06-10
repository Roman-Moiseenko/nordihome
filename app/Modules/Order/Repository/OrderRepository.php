<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\User\Entity\User;
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

    public function getOrders(array $filters)
    {
        $query = Order::orderByDesc('created_at');
        $user_field = $filters['user'] ?? null;
        $condition = $filters['condition'] ?? null;
        $staff = $filters['staff_id'] ?? null;

        if (!is_null($user_field)) {

            $users = User::where('phone', 'like', "%$user_field%")
                ->orWhere('email', 'like', "%$user_field%")
                ->orWhere('fullname', 'like', "%$user_field%")
                ->pluck('id')->toArray();
            $query->whereIn('user_id', $users);
        }


        if (!is_null($condition)) $query->whereHas('status', function ($q) use($condition) {
            $q->where('value' , $condition);
        });

        if (!is_null($staff)) $query->where('manager_id', $staff);
        return $query;
    }

    public function getOrdersByWork(string $filter)
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
                $q->where('value', '>=', OrderStatus::COMPLETED);
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

    /**
     * Возвращает список заказов, которые ждут оплаты, в том числе с внесенной предоплатой
     * @param int|null $order_id - id текущего заказа, для случая, когда надо поменять назначения платежа, для уже оплаченного заказа
     * @return mixed
     */
    public function getNotPaidYet(int $order_id = null): mixed
    {
        $query = Order::whereHas('status', function ($query) {
            $query->where('value', OrderStatus::PREPAID)->orWhere('value', OrderStatus::AWAITING);
        });
        if (!is_null($order_id)) $query->orWhere('id', $order_id);
        return $query->orderBy('number')->get();
    }
}
