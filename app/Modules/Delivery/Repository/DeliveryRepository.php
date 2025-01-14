<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Repository;

use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Repository\OrderRepository;
use Illuminate\Http\Request;

class DeliveryRepository
{

    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAssembly(Request $request, &$filters)
    {
        $query = OrderExpense::where('status', OrderExpense::STATUS_ASSEMBLY);
        $filters = [];
        //TODO Фильтр?
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(OrderExpense $expense) => $this->orderRepository->ExpenseWithToArray($expense));
    }



    public function getExpense(int $type, string $filter)
    {
        $query = OrderExpense::where('type', $type);
        if ($filter == 'new') $query->where('status', OrderExpense::STATUS_ASSEMBLY);
        if ($filter == 'assembly') $query->where('status', OrderExpense::STATUS_ASSEMBLING);
        if ($filter == 'delivery') $query->where('status', OrderExpense::STATUS_DELIVERY);
        if ($filter == 'completed') $query->where('status', OrderExpense::STATUS_COMPLETED);
        return $query;
    }
}
