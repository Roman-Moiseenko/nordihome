<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Order\Service\PaymentService;
use App\Modules\Shop\Cart\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private Cart $cart;
    private PaymentService $payments;
    private DeliveryService $deliveries;
    private OrderService $service;

    public function __construct(Cart $cart, PaymentService $payments, DeliveryService $deliveries, OrderService $service)
    {
        $this->middleware(['auth:user']);
        $this->cart = $cart;
        $this->payments = $payments;
        $this->deliveries = $deliveries;
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $user_id = Auth::guard('user')->user()->id;
        //$ids = $request[''];
        //TODO получаем id выбранных позиций/товаров и создаем cart из выбранных
        $cart = $this->cart->getCartToFront($request['tz']);
        $payments = $this->payments->get($user_id);
        $storages = $this->deliveries->storages();
        $companies = $this->deliveries->companies();

        $default = $this->service->default_user_data();

        $delivery_cost = $this->deliveries->calculate($default->delivery->user_id, $this->cart->getItems());

        return view('shop.order.create', compact('cart', 'payments', 'storages', 'default', 'companies', 'delivery_cost'));
    }

    public function view(Request $request)
    {
        return view('shop.order.view');
    }

    public function index(Request $request)
    {

        return view('shop.order.index');
    }


    public function checkorder(Request $request)
    {
        try {
            $result = $this->service->checkorder($request);

        } catch (\Throwable $e) {
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
        }
        return \response()->json($result);
    }
    ///
}
