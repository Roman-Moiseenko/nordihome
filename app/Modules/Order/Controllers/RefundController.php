<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderRefund;
use App\Modules\Order\Service\RefundService;
use Illuminate\Http\Request;

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
        return redirect()->back()->with('warning', 'В разработке');
        /*
        $query = OrderRefund::orderByDesc('created_at');
        $refunds = $this->pagination($query, $request, $pagination);
        return view('admin.order.refund.index', compact('refunds', 'pagination'));
        */
    }

    public function create(Request $request)
    {

        /** @var Order $order */
        $order = Order::where('number', trim($request['order_id']))->first();
        if (is_null($order)) throw new \DomainException('Заказ № ' . $request['order_id'] . ' не найден');

        //TODO Список всех товаров + услуг, + сколько выдано
        // Список платежей
        // Колонка "На возврат" для денег и товаров/услуг
        return redirect()->back()->with('warning', 'В разработке');
        /*
        if ($order->isCompleted(true) || $order->isPaid() || $order->isPrepaid()) {
            //Создать на возврат товаров
            return view('admin.order.refund.create', compact('order'));
        }
*/
        throw new \DomainException('Для данного заказа нельзя сделать возврат');
    }

    public function store(Order $order, Request $request)
    {
        $params = $request->all();
        $this->service->create($order, $params);
        return redirect()->route('admin.order.refund.index');
    }

    public function show(OrderRefund $refund)
    {
        return redirect()->back()->with('warning', 'В разработке');

        //return view('admin.order.refund.show', compact('refund'));
    }

}
