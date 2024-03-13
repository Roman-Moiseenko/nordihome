<?php


namespace App\Http\Controllers\Admin\Sales;


use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Payment\PaymentOrder;
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

    //TODO Сделать OrderAction и по каждому действию записывать staff->id, Action, json(данные)
    public function destroy(Order $order)
    {
        return $this->try_catch_admin(function () use ($order) {
            $this->service->destroy($order);
            return redirect()->back();
        });
    }

    public function canceled(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->canceled($order, (int)$request['comment']);
            return redirect()->back();
        });
    }

    public function set_manager(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setManager($order, (int)$request['staff_id']);
            return redirect()->back();
        });
    }

    public function set_logger(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setLogger($order, (int)$request['logger_id']);
            return redirect()->back();
        });
    }

    public function set_status(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setStatus($order, (int)$request['status']);
            return redirect()->back();
        });
    }

    public function set_reserve(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setReserve($order, $request['reserve-date'], $request['reserve-time']);
            return redirect()->back();
        });
    }

    public function set_awaiting(Order $order)
    {
        return $this->try_catch_admin(function () use ($order) {
            $this->service->setAwaiting($order);
            return redirect()->back();
        });
    }

    public function set_delivery(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setDelivery($order, (float)$request['delivery-cost']);
            return redirect()->back();
        });
    }

    public function set_moving(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setMoving($order, (int)$request['storage']);
            return redirect()->back();
        });
    }

    public function set_payment(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setPayment($order, $request->all());
            return redirect()->back();
        });
    }

    public function del_payment(PaymentOrder $payment)
    {
        return $this->try_catch_admin(function () use ($payment) {
            $this->service->delPayment($payment);
            return redirect()->back();
        });
    }

    public function paid_payment(Request $request, PaymentOrder $payment)
    {
        return $this->try_catch_admin(function () use ($request, $payment) {
            $this->service->paidPayment($payment, $request['payment-document'] ?? '');
            return redirect()->back();
        });
    }

    //AJAX
    public function set_quantity(Request $request, Order $order)
    {

        return $this->try_catch_ajax_admin(function () use($request, $order) {
            $items = json_decode($request['items'], true);
            $result = $this->service->setQuantity($order, $items);
            return response()->json($result);
        });
    }
}
