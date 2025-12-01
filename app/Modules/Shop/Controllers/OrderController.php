<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Accounting\Repository\StorageRepository;
use App\Modules\Delivery\Helpers\DeliveryHelper;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Repository\PaymentRepository;
use App\Modules\Order\Service\OrderPaymentService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Parser\ParserCart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


/**
 * Контроллер по созданию заказа из клиентской части, для просмотра используется контроллер из User
 */
class OrderController extends ShopController
{
    private Cart $cart;
    private OrderPaymentService $payments;
    private DeliveryService $deliveries;
    private OrderService $service;

    private ParserCart $parserCart;
    private StorageRepository $storages;
    private PaymentRepository $paymentRepository;

    public function __construct(
        Cart              $cart,
        ParserCart        $parserCart,
        OrderPaymentService    $payments,
        PaymentRepository $paymentRepository,
        DeliveryService   $deliveries,
        OrderService      $service,
        StorageRepository $storages,
    )
    {
        parent::__construct();
        $this->middleware('auth:user')->except(['create_cart', 'create_click']);
        $this->cart = $cart;
        $this->payments = $payments;
        $this->deliveries = $deliveries;
        $this->service = $service;
        $this->parserCart = $parserCart;

        $this->storages = $storages;
        $this->paymentRepository = $paymentRepository;
    }

    public function create(Request $request): \Illuminate\View\View
    {

        if (Auth::guard('user')->check()) {
            $user_id = Auth::guard('user')->user()->id;
        } else {
            throw new \DomainException('Доступ ограничен');
        }
        $preorder = true;

        if ($request->has('preorder') && ($request->get('preorder') == "false")) {
            $preorder = false;
        }

        $cart = $this->cart->getCartToFront($request['tz'], $preorder);

        $payments = $this->paymentRepository->getPayments();
        $storages = $this->storages->getPointDelivery();
        $companies = DeliveryHelper::deliveries();
        $delivery_cost = $this->deliveries->calculate($user_id, $this->cart->getItems());

        return view($this->route('order.create'), compact('cart', 'payments',
            'storages', 'companies', 'delivery_cost', 'preorder'));

    }

    public function create_cart(Request $request)
    {
        $order = $this->service->create_cart($request);
        //Для eCommerce - Вынести в
        $e_array = [];
        foreach ($order->items as $item) {
            $e_array[] = [
                'id' => $item->product->id,
                'quantity' => $item->quantity,
            ];
        }

        return view($this->route('order.new'), compact('order', 'e_array'))->with('success', 'Ваш заказ успешно создан!');

       // return redirect()->route('order.new', compact('array'))->with('success', 'Ваш заказ успешно создан!');
    }

    public function create_parser(Request $request)
    {
        if (Auth::guard('user')->check()) {
            $user_id = Auth::guard('user')->user()->id;
        } else {
            throw new \DomainException('Доступ ограничен');
        }
        $payments = $this->paymentRepository->getPayments();
        $storages = $this->storages->getPointDelivery();
        $companies = DeliveryHelper::deliveries();
        $delivery_cost = $this->deliveries->calculate($user_id, $this->parserCart->getItems());
        $cart = $this->parserCart;
        return view($this->route('order.create-parser'), compact('cart', 'payments',
            'storages', 'companies', 'delivery_cost'));

    }

    public function create_click(Request $request)
    {
        $order = $this->service->create_click($request);

        //->route('cabinet.order.view', $order)
        return redirect()->back()->with('success', 'Ваш заказ успешно создан!');
    }

    public function store_parser(Request $request)
    {
        $order = $this->service->create_parser($request);

        return redirect()->route('cabinet.order.view', $order)->with('success', 'Ваш заказ успешно создан!');
    }

    public function create_pre(Request $request)
    {
        //Аналог create + стоимость доставки из польши
    }

    public function store(Request $request)
    {
        $order = $this->service->create($request);
        return redirect()->route('cabinet.order.new_order', ['order' => $order, 'from' => 'store'])->with('success', 'Ваш заказ успешно создан!');
    }

    /*
        public function view(Request $request, Order $order)
        {

            return view('shop.order.view', compact('der'));
        }

        public function index(Request $request)
        {
            $orders = Order::where('user_id', Auth::guard('user')->user()->id)->orderByDesc('updated_at')->get();
            return view('shop.order.index', compact('orders'));
        }
    */


    //AJAX
    public function checkorder(Request $request)
    {
        $result = $this->service->checkorder($request['data']);
        return \response()->json($result);
    }

    public function coupon(Request $request)
    {
        $result = 0;
        if ($request->has('code')) $result = $this->service->coupon($request->get('code'));
        return \response()->json($result);
    }
}
