<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Repository\PaymentRepository;
use App\Modules\Order\Service\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{

    private PaymentService $service;
    private PaymentRepository $repository;
    private OrderRepository $orders;
    private StaffRepository $staffs;

    public function __construct(
        PaymentService    $service,
        PaymentRepository $repository,
        OrderRepository   $orders,
        StaffRepository   $staffs,
    )
    {
        $this->middleware(['auth:admin', 'can:payment']);
        $this->service = $service;
        $this->repository = $repository;
        $this->orders = $orders;
        $this->staffs = $staffs;
    }

    public function index(Request $request): Response
    {
        $payments = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();

        return Inertia::render('Order/Payment/Index', [
            'payments' => $payments,
            'filters' => $filters,
            'staffs' => $staffs,
        ]);
    }

    public function create()
    {
        $methods = PaymentHelper::payments();
        $orders = $this->orders->getNotPaidYet();
        return view('admin.order.payment.create', compact('methods', 'orders'));
    }

    public function store(Request $request)
    {
        $payment = $this->service->create($request->only(['order', 'amount', 'method', 'document']));
        return redirect()->route('admin.order.payment.index');
    }

    public function edit(OrderPayment $payment)
    {
        $methods = PaymentHelper::payments();
        $orders = $this->orders->getNotPaidYet($payment->order_id);
        return view('admin.order.payment.edit', compact('payment', 'methods', 'orders'));
    }

    public function update(OrderPayment $payment, Request $request)
    {
        $this->service->update($payment, $request);
        return redirect()->route('admin.order.payment.index');
    }

    public function show(OrderPayment $payment)
    {
        return redirect()->route('admin.order.payment.index');
    }

    public function destroy(OrderPayment $payment)
    {
        $this->service->destroy($payment);
        return redirect()->route('admin.order.payment.index');
    }
}
