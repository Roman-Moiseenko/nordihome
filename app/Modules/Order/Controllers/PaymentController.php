<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Repository\StorageRepository;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Repository\PaymentRepository;
use App\Modules\Order\Service\OrderPaymentService;
use App\Modules\Order\Service\PaymentService;
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
        OrderPaymentService    $service,
        PaymentRepository $repository,
        OrderRepository   $orders,
        StaffRepository   $staffs,
        StorageRepository $storages
    )
    {
        $this->middleware(['auth:admin', 'can:payment']);
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

    public function update(OrderPayment $payment, Request $request)
    {
        $this->service->update($payment, $request);
        return redirect()->route('admin.order.payment.index');
    }

    public function show(OrderPayment $payment): Response
    {
        return Inertia::render('Order/Payment/Show', [
            'payment' => $this->repository->PaymentWithToArray($payment),
            'methods' => array_select(OrderPayment::METHODS),
            'storages' => $this->storages->getPointSale(),
        ]);
    }

    public function destroy(OrderPayment $payment)
    {
        $this->service->destroy($payment);
        return redirect()->route('admin.order.payment.index');
    }

    public function set_info(OrderPayment $payment, Request $request)
    {
        $this->service->setInfo($payment, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }
}
