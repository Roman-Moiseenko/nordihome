<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Delivery\Service\CalendarService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Service\ExpenseService;
use App\Modules\Service\Report\Trade12Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/**
 * Контроллер работы с выдачей товара OrderExpense для Менеджера
 */
class ExpenseController extends Controller
{
    private ExpenseService $service;
    private Trade12Report $report;
    private OrderRepository $repository;
    private CalendarService $calendar;


    public function __construct(
        ExpenseService $service,
        OrderRepository $repository,
        Trade12Report $report,
        CalendarService $calendar,
    )
    {
        $this->middleware(['auth:admin', 'can:order']);
        $this->service = $service;
        $this->report = $report;
        $this->repository = $repository;
        $this->calendar = $calendar;
    }


    public function show(OrderExpense $expense)
    {
        return Inertia::render('Order/Expense/Show', [
            'expense' => $this->repository->ExpenseWithToArray($expense),
            'reasons' => array_select(OrderExpenseRefund::REASONS),
        ]);

    }

    public function canceled(OrderExpense $expense): RedirectResponse
    {
        $order = $this->service->cancel($expense);
        return redirect()->route('admin.order.show', $order)->with('success', 'Распоряжение удалено. Товар возвращен в резерв и хранилище');
    }

    public function set_delivery(OrderExpense $expense, Request $request): RedirectResponse
    {
        $this->calendar->attach_expense($expense, $request->integer('period_id'));
        return redirect()->back()->with('success', 'Дата отгрузки установлена');
    }

    public function trade12(OrderExpense $expense)
    {
        try {
            $file = $this->report->xlsx($expense);
            $headers = [
                'filename' => basename($file),
            ];
            ob_end_clean();
            ob_start();
            return response()->file($file, $headers);
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

    }


    public function create(Order $order, Request $request): RedirectResponse
    {
        $expense = $this->service->create_expense($order, $request);
        if ($request->input('method') == 'shop') return redirect()->back()->with('success', 'Товар выдан');

        return redirect()->route('admin.order.expense.show', $expense)->with('success', 'Распоряжение сформировано');
    }

    public function set_info(OrderExpense $expense, Request $request): RedirectResponse
    {
        $this->service->setInfo($expense, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function assembly(OrderExpense $expense): RedirectResponse
    {
        $this->service->assembly($expense);
        return redirect()->back()->with('success', 'Распоряжение отправлено на склад на сборку.');
    }
}
