<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;


use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Helpers\OrderHelper;
use App\Modules\User\Entity\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

class OrderRepository
{
    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Order::orderByDesc('created_at');
        $filters = [];

        if ($request->string('user') != '') {
            $user = $request->string('user')->trim()->value();
            $filters['user'] = $user;
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('phone', 'LIKE', "%$user%")
                    ->orWhere('email', 'like', "%$user%")
                    ->orWhereRaw("LOWER(fullname) like LOWER('%$user%')");
            });
        }
        if ($request->string('comment') != '') {
            $comment = $request->string('comment')->trim()->value();
            $filters['comment'] = $comment;
            $query->where('comment', 'like', "%$comment%");
        }
        if ($request->integer('condition') > 0) {
            $condition = $request->integer('condition');
            $filters['condition'] = $condition;
            $query->whereHas('status', function ($q) use($condition) {
                $q->where('value' , $condition);
            });
        }
        if ($request->integer('manager_id') > 0) {
            $manager_id = $request->integer('manager_id');
            $filters['manager_id'] = $manager_id;
            $query->where('manager_id', $manager_id);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('p', 20))
            ->withQueryString()
            ->through(fn(Order $order) => [
                'id' => $order->id,
                'opl' => OrderHelper::pictogram($order),
                'otg' => OrderHelper::pictogram($order),
                'number' => $order->htmlNum(),
                'date' => $order->htmlDate(),
                'manager' => is_null($order->manager_id) ? 'Не назначен' : $order->manager->fullname->getShortname(),
                'user' => $order->user->getPublicName(),
                'amount' => price($order->getTotalAmount()),
                'status' => $order->status->value,
                'status_html' => OrderHelper::status($order),

                'url' => route('admin.order.show', $order),
                'destroy' => route('admin.order.destroy', $order),
                'log' => route('admin.order.log', $order),
            ]);
    }

    #[Deprecated]
    public function getOrders(array $filters)
    {
        $query = Order::orderByDesc('created_at');
        $user_field = $filters['user'];
        $condition = $filters['condition'];
        $staff = $filters['staff_id'];
        $comment = $filters['comment'];

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

        if (!is_null($comment)) $query->where('comment', 'like', "%$comment%");
        //dd($filters);
        return $query;
    }

    #[Deprecated]
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
    #[Deprecated]
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
