<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Service\ExpenseService;
use Illuminate\Http\Request;

class ExpanseController extends Controller
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

    //Через AJAX
    public function store(Request $request)
    {
        return $this->try_catch_ajax(function () use ($request) {
            $data = json_decode($request['data'], true);
            $expanse = $this->service->create($data);
            return response()->json(route('admin.sales.expanse.show', $expanse));
        });
    }

    public function show(OrderExpense $expense)
    {
        return view('admin.sales.expanse.show', compact('expense'));
    }
}
