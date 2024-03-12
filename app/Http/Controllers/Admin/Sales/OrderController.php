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
        $this->service->destroy($order);
        return redirect()->back();
    }

    public function canceled(Request $request, Order $order)
    {
        $this->service->canceled($order, $request['comment']);
        return redirect()->back();
    }

    public function set_manager(Request $request, Order $order)
    {
        $this->service->setManager($order, (int)$request['staff_id']);
        return redirect()->back();
    }

    public function sel_logger(Request $request, Order $order)
    {
        $this->service->setLogger($order, (int)$request['logger_id']);
        return redirect()->back();
    }

    public function set_status(Request $request, Order $order)
    {
        $this->service->setStatus($order, (int)$request['status']);
        return redirect()->back();
    }

    public function set_reserve(Request $request, Order $order)
    {
        $this->service->setReserve($order, $request['reserve-date'], $request['reserve-time']);
        return redirect()->back();
    }

    public function set_awaiting(Order $order)
    {
        try {
            $this->service->setAwaiting($order);

        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            flash($e->getMessage(), 'danger');

        }

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

    public function set_payment(Request $request, Order $order)
    {
        $params = $request->all();
        $this->service->setPayment($order, $params);
        return redirect()->back();
    }

    public function del_payment(PaymentOrder $payment)
    {
        $this->service->delPayment($payment);
        return redirect()->back();
    }

    public function paid_payment(Request $request, PaymentOrder $payment)
    {
        $document = $request['payment-document'] ?? '';
        $this->service->paidPayment($payment, $document);
        return redirect()->back();
    }

    //AJAX
    public function set_quantity(Request $request, Order $order)
    {
        try {
            $items = json_decode($request['items'], true);
            $result = $this->service->setQuantity($order, $items);
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json($e->getMessage());
        }
    }
}
