<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Service\ExpenseService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    private ExpenseService $service;

    public function __construct(ExpenseService $service)
    {
        $this->middleware(['auth:admin', 'can:order']);
        $this->service = $service;
    }

    public function create()
    {
        flash('Данный метод не должен вызываться');
        return redirect()->route('home');
    }

    public function show(OrderExpense $expense)
    {
        return view('admin.sales.expense.show', compact('expense'));
    }

    public function destroy(OrderExpense $expense) {
        return $this->try_catch(function () use ($expense) {
            $order = $this->service->cancel($expense);
            flash('Распоряжение успешно удалено. Товар возвращен в резерв и хранилище.', 'info');
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    //Через AJAX
    public function store(Request $request)
    {
        return $this->try_catch_ajax(function () use ($request) {
            $data = json_decode($request['data'], true);
            $expense = $this->service->create($data);
            return response()->json(route('admin.sales.expense.show', $expense));
        });
    }


}
