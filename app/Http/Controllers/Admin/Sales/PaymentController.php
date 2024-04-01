<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Service\PaymentRepository;
use App\Modules\Order\Service\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    private PaymentService $service;
    private PaymentRepository $repository;
    private OrderRepository $orders;

    public function __construct(PaymentService $service, PaymentRepository $repository, OrderRepository $orders)
    {
        $this->middleware(['auth:admin', 'can:payment']);
        $this->service = $service;
        $this->repository = $repository;
        $this->orders = $orders;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $query = $this->repository->getIndex($request);
            $payments = $this->pagination($query, $request, $pagination);
            return view('admin.sales.payment.index', compact('payments', 'pagination'));
        });
    }

    public function create()
    {
        return $this->try_catch_admin(function () {
            $methods = PaymentHelper::payments();
            $orders = $this->orders->getNotPaidYet();
            /*$orders = Order::whereHas('status', function ($query) {
                $query->where('value', OrderStatus::PREPAID)->orWhere('value', OrderStatus::AWAITING);
            })->orderBy('number')->get();*/
            return view('admin.sales.payment.create', compact('methods', 'orders'));
        });
    }

    public function store(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $payment = $this->service->create($request);
            return redirect()->route('admin.sales.payment.index');
        });
    }

    public function edit(OrderPayment $payment)
    {
        return $this->try_catch_admin(function () use ($payment) {
            $methods = PaymentHelper::payments();
            /*$orders = Order::whereHas('status', function ($query) {
                $query->where('value', OrderStatus::PREPAID)->orWhere('value', OrderStatus::AWAITING);
            })->orWhere('id', $payment->order_id)->orderBy('number')->get();
            */
            $orders = $this->orders->getNotPaidYet($payment->order_id);
            return view('admin.sales.payment.edit', compact('payment', 'methods', 'orders'));
        });
    }

    public function update(OrderPayment $payment, Request $request)
    {
        return $this->try_catch_admin(function () use ($payment, $request) {
            $payment = $this->service->update($payment, $request);
            return redirect()->route('admin.sales.payment.index');
            //return redirect()->route('admin.sales.payment.show', $payment);
        });
    }

    public function show(OrderPayment $payment)
    {
        return $this->try_catch_admin(function () use ($payment) {
            return redirect()->route('admin.sales.payment.index');
            //return view('admin.sales.payment.show', $payment);
        });
    }

    public function destroy(OrderPayment $payment)
    {
        return $this->try_catch_admin(function () use ($payment) {
            $this->service->destroy($payment);
            return redirect()->route('admin.sales.payment.index');
        });
    }
}
