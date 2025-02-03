<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Repository;

use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Delivery\Entity\CalendarPeriod;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Repository\OrderRepository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class DeliveryRepository
{

    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getLoader(Request $request)
    {
        $query = OrderExpense::orderByDesc('updated_at')->orderBy('status')
            ->orderBy('type')
            ->whereIn('status', [OrderExpense::STATUS_ASSEMBLY, OrderExpense::STATUS_ASSEMBLING, OrderExpense::STATUS_ASSEMBLED]);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(OrderExpense $expense) => $this->orderRepository->ExpenseWithToArray($expense));
    }


    public function getExpense(int $type, string $filter): OrderExpense
    {
        $query = OrderExpense::where('type', $type);
        if ($filter == 'new') $query->where('status', OrderExpense::STATUS_ASSEMBLY);
        if ($filter == 'assembly') $query->where('status', OrderExpense::STATUS_ASSEMBLING);
        if ($filter == 'delivery') $query->where('status', OrderExpense::STATUS_DELIVERY);
        if ($filter == 'completed') $query->where('status', OrderExpense::STATUS_COMPLETED);
        return $query;
    }

    public function getLocal(): Arrayable
    {
        $query = OrderExpense::orderByDesc('updated_at')->orderBy('status')
            ->where('type', OrderExpense::DELIVERY_LOCAL)
            ->whereIn('status', [OrderExpense::STATUS_DELIVERY, OrderExpense::STATUS_DELIVERED, OrderExpense::STATUS_ASSEMBLED]);
        return $query->get()->map(fn(OrderExpense $expense) => $this->orderRepository->ExpenseWithToArray($expense));
    }

    public function getRegion(): Arrayable
    {
        $query = OrderExpense::orderByDesc('updated_at')->orderBy('status')
            ->where('type', OrderExpense::DELIVERY_REGION)->whereHas('order', function ($query) {
                $query->where('type', '<>', Order::OZON);
            })
            ->whereIn('status', [OrderExpense::STATUS_DELIVERY, OrderExpense::STATUS_DELIVERED, OrderExpense::STATUS_ASSEMBLED]);
        return $query->get()->map(fn(OrderExpense $expense) => $this->DeliveryItemToArray($expense));
    }

    public function getOzon(): Arrayable
    {
        $query = OrderExpense::orderByDesc('updated_at')->orderBy('status')
            ->where('type', OrderExpense::DELIVERY_REGION)->whereHas('order', function ($query) {
                $query->where('type', Order::OZON);
            })
            ->whereIn('status', [OrderExpense::STATUS_DELIVERY, OrderExpense::STATUS_DELIVERED, OrderExpense::STATUS_ASSEMBLED]);
        return $query->get()->map(fn(OrderExpense $expense) => $this->orderRepository->ExpenseWithToArray($expense));
    }

    private function DeliveryItemToArray(OrderExpense $expense): array
    {
        return array_merge($expense->toArray(), [
            'visible_cargo' => false,
            'delivery' => $expense->delivery()->with('cargo')->first(),
            'driver' => $expense->getDriver(),
        ]);
    }


    public function getAll(Request $request, &$filters)
    {
        $query = OrderExpense::orderByDesc('created_at')->orderBy('status')
            ->orderBy('type')
            ->whereNotIn('status', [OrderExpense::STATUS_CANCELED]);
        $filters = [];
        //TODO Фильтр?
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(OrderExpense $expense) => $this->orderRepository->ExpenseWithToArray($expense));
    }

    public function getCalendar(bool $new = true): Arrayable
    {
        $sign = $new ? '>=' : '<';
        return Calendar::orderBy('date_at')
            ->where('date_at', $sign, now()->toDateString())
            ->whereHas('periods', function ($query) {
                $query->whereHas('expenses', function ($query) {
                    $query->where('status', '<>', OrderExpense::STATUS_COMPLETED);
                });
            })->get()
            ->map(function (Calendar $calendar) {
                $is_drivers = true;
                $expenses = OrderExpense::where('status', '<>', OrderExpense::STATUS_COMPLETED)
                    ->whereHas('calendarPeriods', function ($query) use ($calendar) {
                        $query->whereHas('calendar', function ($query) use ($calendar) {
                            $query->where('id', $calendar->id);
                        });
                    })->getModels();
                $count = count($expenses);
                foreach ($expenses as $expense) {
                    if ($expense->getDriver() == null) $is_drivers = false;
                }

                return array_merge($calendar->toArray(), [
                    'count' => $count,
                    'is_drivers' => $is_drivers,
                    'periods' => $calendar->periods()
                        ->whereHas('expenses', function ($query) {
                            $query->where('status', '<>', OrderExpense::STATUS_COMPLETED);
                        })
                        ->get()->map(function (CalendarPeriod $period) {
                            return array_merge($period->toArray(), [
                                'time_text' => $period->timeHtml(),
                                'expenses' => $period->expenses()->get()->map(function (OrderExpense $expense) {
                                    return array_merge($expense->toArray(), [
                                        'visible_driver' => false,
                                        'driver' => $expense->getDriver(),
                                        'is_assemble' => $expense->isAssemble(),
                                        'assembles' => $expense->getAssemble(),
                                        'visible_assemble' => false,
                                        'visible_loader' => false,
                                    ]);
                                }),
                            ]);
                        }),
                ]);
            });
    }
}
