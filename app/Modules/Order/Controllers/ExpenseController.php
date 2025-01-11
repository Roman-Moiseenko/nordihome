<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Service\ExpenseService;
use App\Modules\Service\Report\Trade12Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    private ExpenseService $service;
    private Trade12Report $report;

    public function __construct(ExpenseService $service, Trade12Report $report)
    {
        $this->middleware(['auth:admin', 'can:order']);
        $this->service = $service;
        $this->report = $report;
    }


    public function show(OrderExpense $expense)
    {
        return view('admin.order.expense.show', compact('expense'));
    }

    public function destroy(OrderExpense $expense)
    {
        $order = $this->service->cancel($expense);
        flash('Распоряжение успешно удалено. Товар возвращен в резерв и хранилище.', 'info');
        return redirect()->route('admin.order.show', $order);
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

        return redirect()->json(route('admin.order.expense.show', $expense))->with('success', 'Распоряжение сформировано');
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
