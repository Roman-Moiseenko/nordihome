<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Delivery\Repository\DeliveryRepository;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Service\ExpenseService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use JetBrains\PhpStorm\ArrayShape;

class DeliveryController extends Controller
{
    private ExpenseService $expenseService;
    private DeliveryRepository $repository;

    public function __construct(ExpenseService $expenseService, DeliveryRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:delivery', 'can:order']);
        $this->expenseService = $expenseService;
        $this->repository = $repository;
    }

    public function assembly(Request $request)
    {

        $expenses = $this->repository->getAssembly($request, $filters);
        return Inertia::render('Delivery/Assembly/Index', [
            'expenses' => $expenses,
            'filters' => $filters,
            'works' => Worker::where('active', true)->get(),
        ]);


    }

    public function index_local(Request $request)
    {
        return $this->view($request, OrderExpense::DELIVERY_LOCAL);
    }

    public function index_region(Request $request)
    {
        return $this->view($request, OrderExpense::DELIVERY_REGION);
    }

    public function index_storage(Request $request)
    {
        return $this->view($request, OrderExpense::DELIVERY_STORAGE);
    }

    private function view(Request $request, $type)
    {
        $workers = Worker::where('active', true)->get();
        $filter = $request['filter'] ?? 'new';
        $filter_count = $this->getFilterCount($type);

        $query = $this->repository->getExpense($type, $filter);
        $expenses = $this->pagination($query, $request, $pagination);
        return view('admin.delivery.index', compact('expenses', 'type', 'filter_count', 'filter', 'workers', 'pagination'));

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

    public function completed(OrderExpense $expense)
    {
        $this->expenseService->completed($expense);
        return redirect()->back();
    }

    public function assembling(Request $request, OrderExpense $expense)
    {
        $this->expenseService->assembling($expense, $request->integer('worker_id'));
        return redirect()->back();
    }

    public function delivery(Request $request, OrderExpense $expense)
    {
        $this->expenseService->delivery($expense, $request['track'] ?? '');
        return redirect()->back();
    }
}
