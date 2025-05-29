<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Repository\StorageRepository;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Repository\PaymentRepository;
use App\Modules\Order\Service\OrderPaymentService;
use App\Modules\Order\Service\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{

    private OrderPaymentService $service;
    private PaymentRepository $repository;
    private OrderRepository $orders;
    private StaffRepository $staffs;
    private StorageRepository $storages;

    public function __construct(
        OrderPaymentService $service,
        PaymentRepository   $repository,
        OrderRepository     $orders,
        StaffRepository     $staffs,
        StorageRepository   $storages
    )
    {
        $this->middleware(['auth:admin', 'can:payment', 'can:order']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;
        $this->orders = $orders;
        $this->staffs = $staffs;
        $this->storages = $storages;
    }

    public function index(Request $request): Response
    {
        $payments = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();

        return Inertia::render('Order/Payment/Index', [
            'payments' => $payments,
            'filters' => $filters,
            'staffs' => $staffs,
            'methods' => array_select(OrderPayment::METHODS),
        ]);
    }

    public function create(Order $order, Request $request): RedirectResponse
    {
        $payment = $this->service->createByOrder($order, $request->string('method')->value());
        return redirect()->route('admin.order.payment.show', $payment);

    }

    public function create_refund(OrderExpenseRefund $refund, Request $request): RedirectResponse
    {
        $payment = $this->service->createByRefund($refund);
        return redirect()->route('admin.order.payment.show', $payment);
    }

    public function show(OrderPayment $payment): Response
    {
        return Inertia::render('Order/Payment/Show', [
            'payment' => $this->repository->PaymentWithToArray($payment),
            'methods' => array_select(OrderPayment::METHODS),
            'storages' => $this->storages->getPointSale(),
            'order_related' => $payment->order->relatedDocuments(),
        ]);
    }

    public function destroy(OrderPayment $payment)
    {
        $this->service->destroy($payment);
        return redirect()->route('admin.order.payment.index');
    }

    public function completed(OrderPayment $payment): RedirectResponse
    {
        $this->service->completed($payment);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function work(OrderPayment $payment): RedirectResponse
    {
        $this->service->work($payment);
        return redirect()->back()->with('success', 'Документ в работе');
    }

    public function set_info(OrderPayment $payment, Request $request): RedirectResponse
    {
        $this->service->setInfo($payment, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function set_order(Order $order, OrderPayment $payment): RedirectResponse
    {
        $this->service->setOrder($order, $payment);
        return redirect()->back()->with('success', 'Платеж назначен');
    }

    public function find(Request $request): JsonResponse
    {
        $payments = OrderPayment::where('order_id', null)
            ->where('shopper_id', $request->integer('shopper_id'))
            ->where('trader_id', $request->integer('trader_id'))
            ->get()->map(function (OrderPayment $payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'purpose' => $payment->bank_payment->purpose,
                ];
            });
        return \response()->json($payments);
    }
}
