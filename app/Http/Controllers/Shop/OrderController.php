<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Events\ThrowableHasAppeared;
use App\Modules\Accounting\Repository\StorageRepository;
use App\Modules\Delivery\Helpers\DeliveryHelper;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Order\Service\PaymentService;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Parser\ParserCart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


/**
 * Контроллер по созданию заказа из клиентской части, для просмотра используется контроллер из User
 */
class OrderController extends Controller
{
    private Cart $cart;
    private PaymentService $payments;
    private DeliveryService $deliveries;
    private OrderService $service;

    private ParserCart $parserCart;
    private StorageRepository $storages;

    public function __construct(
        Cart            $cart,
        ParserCart      $parserCart,
        PaymentService  $payments,
        DeliveryService $deliveries,
        OrderService    $service,
        StorageRepository $storages,
    )
    {
        $this->middleware('auth:user', ['except' => 'create_click']);
        $this->cart = $cart;
        $this->payments = $payments;
        $this->deliveries = $deliveries;
        $this->service = $service;
        $this->parserCart = $parserCart;

        $this->storages = $storages;
    }

    public function create(Request $request)
    {
        return $this->try_catch(function () use ($request) {

            if (Auth::guard('user')->check()) {
                $user_id = Auth::guard('user')->user()->id;
            } else {
                throw new \DomainException('Доступ ограничен');
            }
            $cart = $this->cart->getCartToFront($request['tz']);
            $preorder = 1;
            if ($request->has('preorder') && ($request->get('preorder') == "false")) {//Очищаем корзину от излишков
                $cart['items_preorder'] = [];
                $preorder = 0;
            }
            $payments = $this->payments->get();
            $storages = $this->storages->getPointDelivery();
            $companies = DeliveryHelper::deliveries();
            $delivery_cost = $this->deliveries->calculate($user_id, $this->cart->getItems());

            return view('shop.order.create', compact('cart', 'payments',
                'storages', 'companies', 'delivery_cost', 'preorder'));
        }, route('home'));

    }

    public function create_parser(Request $request)
    {
        return $this->try_catch(function () use ($request) {
            if (Auth::guard('user')->check()) {
                $user_id = Auth::guard('user')->user()->id;
            } else {
                throw new \DomainException('Доступ ограничен');
            }
            $payments = $this->payments->get();
            $storages = $this->storages->getPointDelivery();
            $companies = DeliveryHelper::deliveries();
            $delivery_cost = $this->deliveries->calculate($user_id, $this->parserCart->getItems());
            $cart = $this->parserCart;
            return view('shop.order.create-parser', compact('cart', 'payments',
                'storages', 'companies', 'delivery_cost'));
        }, route('home'));

    }

    public function create_click(Request $request)
    {
        return $this->try_catch(function () use ($request) {
            $order = $this->service->create_click($request);
            flash('Ваш заказ успешно создан!');
            return redirect()->route('cabinet.order.view', $order);
        }, route('home'));
    }

    public function store_parser(Request $request)
    {
        return $this->try_catch(function () use ($request) {
            $order = $this->service->create_parser($request);
            flash('Ваш заказ успешно создан!');
            return redirect()->route('cabinet.order.view', $order);
        }, route('home'));
    }

    public function create_pre(Request $request)
    {
        //Аналог create + стоимость доставки из польши
    }

    public function store(Request $request)
    {
        return $this->try_catch(function () use ($request) {
            $order = $this->service->create($request);
            flash('Ваш заказ успешно создан!');
            return redirect()->route('cabinet.order.view', $order);
        }, route('home'));
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
        return $this->try_catch_ajax(function () use ($request) {
            $result = $this->service->checkorder($request['data']);
            return \response()->json($result);
        });
    }

    public function coupon(Request $request)
    {
        return $this->try_catch_ajax(function () use ($request) {
            $result = 0;
            if ($request->has('code')) $result = $this->service->coupon($request->get('code'));
            return \response()->json($result);
        });

    }
}
