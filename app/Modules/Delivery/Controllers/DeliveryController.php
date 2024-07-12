<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Service\ExpenseService;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;

class DeliveryController extends Controller
{
    private ExpenseService $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->middleware(['auth:admin', 'can:delivery', 'can:order']);
        $this->expenseService = $expenseService;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            //return $this->view($request);
        });
    }

    public function index_local(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            return $this->view($request, OrderExpense::DELIVERY_LOCAL);
        });
    }

    public function index_region(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            return $this->view($request, OrderExpense::DELIVERY_REGION);
        });
    }

    public function index_storage(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            return $this->view($request, OrderExpense::DELIVERY_STORAGE);
        });
    }

    private function view(Request $request, $type)
    {

        $workers = Worker::where('active', true)->get();
        $filter = $request['filter'] ?? 'new';
        //$type = OrderExpense::DELIVERY_LOCAL;
        $filter_count = $this->getFilterCount($type);
        $query = $this->getExpense($type, $filter);
        $expenses = $this->pagination($query, $request, $pagination);
        return view('admin.delivery.index', compact('expenses', 'type', 'filter_count', 'filter','workers', 'pagination'));

    }

    #[ArrayShape(['new' => "int", 'assembly' => "int", 'delivery' => "int"])]
    public function getFilterCount(int $type_delivery): array
    {
        return [
            'new' => OrderExpense::where('type', $type_delivery)->where('status', OrderExpense::STATUS_ASSEMBLY)->count(),
            'assembly' => OrderExpense::where('type', $type_delivery)->where('status', OrderExpense::STATUS_ASSEMBLING)->count(),
            'delivery' => OrderExpense::where('type', $type_delivery)->where('status', OrderExpense::STATUS_DELIVERY)->count(),
        ];
    }

    private function getExpense(int $type, string $filter)
    {
        $query = OrderExpense::where('type', $type);
        if ($filter == 'new') $query->where('status', OrderExpense::STATUS_ASSEMBLY);
        if ($filter == 'assembly') $query->where('status', OrderExpense::STATUS_ASSEMBLING);
        if ($filter == 'delivery') $query->where('status', OrderExpense::STATUS_DELIVERY);
        if ($filter == 'completed') $query->where('status', OrderExpense::STATUS_COMPLETED);
        return $query;
    }


    public function completed(OrderExpense $expense)
    {
        return $this->try_catch_admin(function () use($expense) {
            $this->expenseService->completed($expense);
            return redirect()->back();
        });
    }

    public function assembling(Request $request, OrderExpense $expense)
    {
        return $this->try_catch_admin(function () use($request, $expense) {
            $this->expenseService->assembling($expense, (int)$request['worker_id']);
            return redirect()->back();
        });
    }

    public function delivery(Request $request, OrderExpense $expense)
    {
        return $this->try_catch_admin(function () use($request, $expense) {
            $this->expenseService->delivery($expense, $request['track'] ?? '');
            return redirect()->back();
        });
    }
}
