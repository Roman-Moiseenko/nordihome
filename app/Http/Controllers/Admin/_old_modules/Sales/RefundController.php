<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\_old_modules\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderRefund;
use App\Modules\Order\Service\RefundService;
use Illuminate\Http\Request;
use function redirect;
use function view;

class RefundController extends Controller
{
    private RefundService $service;

    public function __construct(RefundService $service)
    {
        $this->middleware(['auth:admin', 'can:refund']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            //Фильтры ??
            $query = OrderRefund::orderByDesc('created_at');
            $refunds = $this->pagination($query, $request, $pagination);
            return view('admin.sales.refund.index', compact('refunds', 'pagination'));
        });
    }

    public function create(Request $request)
    {
        //TODO
        return $this->try_catch_admin(function () use ($request) {
            /** @var Order $order */
            $order = Order::where('number', trim($request['order_id']))->first();
            if (is_null($order)) throw new \DomainException('Заказ № ' . $request['order_id'] . ' не найден');

            if ($order->isCompleted(true) || $order->isPaid() || $order->isPrepaid()) {
                //Создать на возврат товаров

                return view('admin.sales.refund.create', compact('order'));
            }

            throw new \DomainException('Для данного заказа нельзя сделать возврат');
        });
    }

    public function store(Order $order, Request $request)
    {
        //TODO
        return $this->try_catch_admin(function () use ($order, $request) {

            $params = $request->all();
            $this->service->create($order, $params);
            return redirect()->route('admin.order.refund.index');
        });
    }


    public function show(OrderRefund $refund)
    {
        return $this->try_catch_admin(function () use ($refund) {
            return view('admin.order.refund.show', compact('refund'));
        });
    }


}
