<?php


namespace App\Http\Controllers\Admin\Sales;


use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Service\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Общие операции с моделью Order. Все запросы POST или DELETE
 * Class OrderController
 * @package App\Http\Controllers\Admin\Sales
 *
 */
class OrderController extends Controller
{

    private SalesService $service;

    public function __construct(SalesService $service)
    {
        $this->service = $service;
    }

    //TODO
    public function destroy(Order $order)
    {
        $this->service->destroy($order);
        return redirect()->back();
    }

    public function canceled(Order $order)
    {
        $this->service->canceled($order);
        return redirect()->back();
    }

    public function set_manager(Request $request, Order $order)
    {
        $this->service->setManager($order, (int)$request['staff_id']);
        return redirect()->back();
    }

    public function set_reserve(Request $request, Order $order)
    {
        $this->service->setReserve($order, $request['reserve-date'], $request['reserve-time']);
        return redirect()->back();
    }

    public function to_pay(Order $order)
    {
        $this->service->toOrder($order);
        return redirect()->back();
    }

    public function set_delivery(Request $request, Order $order)
    {
        $cost = (float)$request['delivery-cost'];
        $this->service->setDelivery($order, $cost);
        return redirect()->back();
    }

    public function set_moving(Request $request, Order $order)
    {
        $storage_id = (int)$request['storage'];
        $this->service->setMoving($order, $storage_id);
        return redirect()->back();
    }

    //AJAX
    public function set_quantity(Request $request, Order $order)
    {
        $items = json_decode($request['items'], true);
        $result = $this->service->setQuantity($order, $items);
        return response()->json($result);
    }
}
