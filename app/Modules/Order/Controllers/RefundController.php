<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use App\Modules\Order\Entity\Order\OrderExpenseRefundAddition;
use App\Modules\Order\Entity\Order\OrderExpenseRefundItem;
use App\Modules\Order\Repository\RefundRepository;
use App\Modules\Order\Service\RefundService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RefundController extends Controller
{
    private RefundService $service;
    private RefundRepository $repository;
    private StaffRepository $staffs;

    public function __construct(
        RefundService    $service,
        StaffRepository  $staffs,
        RefundRepository $repository,
    )
    {
        $this->middleware(['auth:admin', 'can:refund']);
        $this->service = $service;
        $this->repository = $repository;
        $this->staffs = $staffs;
    }

    public function index(Request $request): Response
    {
        $refunds = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_REFUND);
        return Inertia::render('Order/Refund/Index', [
            'refunds' => $refunds,
            'filters' => $filters,
            'staffs' => $staffs,
        ]);

    }

    public function store(OrderExpense $expense, Request $request): RedirectResponse
    {
        $refund = $this->service->create($expense, $request);
        return redirect()->route('admin.order.refund.show', $refund)->with('success', 'Сохранено');
    }

    public function show(OrderExpenseRefund $refund): Response
    {
        return Inertia::render('Order/Refund/Show', [
            'refund' => $this->repository->RefundWithToArray($refund),
            'reasons' => array_select(OrderExpenseRefund::REASONS),
            'order_related' => $refund->expense->order->relatedDocuments(),

        ]);

    }

    public function completed(OrderExpenseRefund $refund): RedirectResponse
    {
        $this->service->completed($refund);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function throw(OrderExpenseRefund $refund): RedirectResponse
    {
        $this->service->throw($refund);
        return redirect()->back()->with('success', 'Документ сброшен до начальных значений');
    }

    public function set_info(OrderExpenseRefund $refund, Request $request): RedirectResponse
    {
        $this->service->setInfo($refund, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function set_item(OrderExpenseRefundItem $item, Request $request): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_item(OrderExpenseRefundItem $item): RedirectResponse
    {
        $this->service->delItem($item);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function set_addition(OrderExpenseRefundAddition $addition, Request $request): RedirectResponse
    {
        $this->service->setAddition($addition, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_addition(OrderExpenseRefundAddition $item): RedirectResponse
    {
        $this->service->delAddition($item);
        return redirect()->back()->with('success', 'Удалено');
    }

}
