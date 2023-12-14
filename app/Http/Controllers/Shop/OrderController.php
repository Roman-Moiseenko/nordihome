<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Order\Service\PaymentService;
use App\Modules\Shop\Cart\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


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
        //TODO !!!!!
        //Постановка в Резерв товара
        //Если товара меньше, чем есть и стоит флаг Только по наличию - обрезаем кол-во

        if ($request->has('preorder') && ($request->get('preorder') == "false") ) {//Очищаем корзину от излишков
            $this->cart->loadItems();
            if ($this->cart->info->preorder) $this->cart->setAvailability();
        }

        $cart = $this->cart->getCartToFront($request['tz']);
        $payments = $this->payments->get();
        $storages = $this->deliveries->storages();
        $companies = $this->deliveries->companies();

        $default = $this->service->default_user_data();

        $delivery_cost = $this->deliveries->calculate($default->delivery->user_id, $this->cart->getItems());

        return view('shop.order.create', compact('cart', 'payments',
            'storages', 'default', 'companies', 'delivery_cost'));
    }

    public function create_pre(Request $request)
    {
        //Аналог create + стоимость доставки из польши
    }

    public function store(Request $request)
    {
        $order = $this->service->create($request);
        flash('Ваш заказ успешно создан!');
        return redirect()->route('shop.order.view', $order);
    }

    public function view(Request $request)
    {
        return view('shop.order.view');
    }

    public function index(Request $request)
    {

        return view('shop.order.index');
    }

    //AJAX
    public function checkorder(Request $request)
    {
        try {
            $result = $this->service->checkorder($request['data']);
        } catch (\Throwable $e) {
            $result = ['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]];
        }
        return \response()->json($result);
    }

    public function coupon(Request $request)
    {
        $result = 0;
        try {
            if ($request->has('code')) $result = $this->service->coupon($request->get('code'));
        } catch (\Throwable $e) {
            $result = ['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]];
        }
        return \response()->json($result);
    }
}
