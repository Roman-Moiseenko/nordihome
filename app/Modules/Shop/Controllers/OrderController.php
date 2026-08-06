<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;


use App\Modules\Accounting\Repository\StorageRepository;
use App\Modules\Delivery\Helpers\DeliveryHelper;
use App\Modules\Delivery\Service\DeliveryService;

use App\Modules\Order\Application\Services\CreateOrderFromCartService;
use App\Modules\Order\Repository\PaymentRepository;
use App\Modules\Order\Service\OrderPaymentService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Shop\Application\Actions\Cart\GetCartUseCase;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Parser\ParserCart;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


/**
 * Контроллер по созданию заказа из клиентской части, для просмотра используется контроллер из User
 */
class OrderController extends \App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController
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
        private GetCartUseCase $getCartUseCase,
        private CreateOrderFromCartService $createOrderFromCartService,
    )
    {
       // parent::__construct();
        //$this->middleware('auth:user')->except(['create_cart', 'create_click']);
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
/*
        if (Auth::guard('web')->check()) {
            $user_id = Auth::guard('web')->user()->id;
        } else {
            throw new \DomainException('Доступ ограничен');
        }
        $preorder = true;

        if ($request->has('preorder') && ($request->get('preorder') == "false")) {
            $preorder = false;
        }
*/


        //$cart = $this->cart->getCartToFront($request['tz']);
        //$client = $this->getClient($request);

        $payments = $this->paymentRepository->getPayments();
        $storages = $this->storages->getPointDelivery();
        $companies = DeliveryHelper::deliveries();
        //$delivery_cost = $this->deliveries->calculate($client->id, $this->cart->getItems());

        $cartInfo = $this->getCartUseCase->execute();

        return view('shop.order.create', compact('cartInfo', 'payments',
            'storages', 'companies'));

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

        return view('shop.cabinet.order.new', compact('order', 'e_array'))->with('success', 'Ваш заказ успешно создан!');

       // return redirect()->route('order.new', compact('array'))->with('success', 'Ваш заказ успешно создан!');
    }
/*
    public function create_parser(Request $request)
    {
        if (Auth::guard('web')->check()) {
            $user_id = Auth::guard('web')->user()->id;
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

    }*/

    public function create_click(Request $request)
    {
        $order = $this->service->create_click($request);

        //->route('cabinet.order.view', $order)
        return redirect()->back()->with('success', 'Ваш заказ успешно создан!');
    }


    public function create_pre(Request $request)
    {
        //Аналог create + стоимость доставки из польши
    }

    public function store(Request $request)
    {

        $client = $this->getClient($request);
        $order = $this->createOrderFromCartService->execute($client, $request->input('coupon'));
        //return null;
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
            $orders = Order::where('user_id', Auth::guard('web')->user()->id)->orderByDesc('updated_at')->get();
            return view('shop.order.index', compact('orders'));
        }
    */


    //AJAX
    public function checkorder(Request $request)
    {
        \Log::info(json_encode($request->all()));
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
