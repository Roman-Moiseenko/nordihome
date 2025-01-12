<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Service\ExpenseService;
use App\Modules\Service\Report\Trade12Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    private ExpenseService $service;
    private Trade12Report $report;
    private OrderRepository $repository;


    public function __construct(ExpenseService $service, OrderRepository $repository, Trade12Report $report)
    {
        $this->middleware(['auth:admin', 'can:order']);
        $this->service = $service;
        $this->report = $report;
        $this->repository = $repository;
    }


    public function show(OrderExpense $expense)
    {
        return Inertia::render('Order/Expense/Show', [
            'expense' => $this->repository->ExpenseWithToArray($expense),

        ]);

    }

    public function canceled(OrderExpense $expense): RedirectResponse
    {
        $order = $this->service->cancel($expense);
        return redirect()->route('admin.order.show', $order)->with('success', 'Распоряжение удалено. Товар возвращен в резерв и хранилище');
    }


    public function trade12(OrderExpense $expense)
    {
        $file = $this->report->xlsx($expense);
        ob_end_clean();
        ob_start();
        return response()->file($file);
    }


    public function create(Order $order, Request $request)
    {
        $expense = $this->service->create_expense($order, $request);
        if ($request->input('method') == 'shop') return redirect()->back()->with('success', 'Товар выдан');

        return redirect()->route('admin.order.expense.show', $expense)->with('success', 'Распоряжение сформировано');
    }

    public function set_info(OrderExpense $expense, Request $request)
    {
        $this->service->setInfo($expense, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

/*
    public function issue_shop(Request $request)
    {
        $data = json_decode($request['data'], true);

        try {
            $expense = $this->service->issue_shop($data);
            flash('Товар выдан', 'info');
            return response()->json(route('admin.order.show', $expense->order));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
/*
    public function issue_warehouse(Request $request)
    {
        $data = json_decode($request['data'], true);
        $expense = $this->service->issue_warehouse($data);
        flash('Заявка на выдачу сформирована. Распечатайте накладную', 'info');
        return response()->json(route('admin.order.expense.show', $expense));
    }
*/
    public function assembly(OrderExpense $expense)
    {
        $order = $this->service->assembly($expense);
        flash('Распоряжение отправлено на склад на сборку.', 'info');
        return redirect()->route('admin.order.show', $order);
    }
}
