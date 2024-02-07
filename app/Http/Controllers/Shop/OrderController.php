<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Admin\Entity\Options;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Order\Service\OrderService;
use App\Modules\Order\Service\PaymentService;
use App\Modules\Order\Service\ReserveService;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\User\Entity\CartStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
    private ReserveService $reserves;
    private $minutes;
    private ParserCart $parserCart;

    public function __construct(
        Cart $cart,
        ParserCart $parserCart,
        PaymentService $payments,
        DeliveryService $deliveries,
        OrderService $service,
        ReserveService  $reserves
    )
    {
        $this->middleware(['auth:user']);
        $this->cart = $cart;
        $this->payments = $payments;
        $this->deliveries = $deliveries;
        $this->service = $service;
        $this->reserves = $reserves;
        $this->minutes = (new Options())->shop->reserve_cart;
        $this->parserCart = $parserCart;
    }

    public function create(Request $request)
    {
        //TODO !!!!! Перенести куда-нибудь
        //Постановка в Резерв товара
        foreach ($this->cart->getOrderItems() as $item) {
            if ($item->reserve == null) {
                $reserve = $this->reserves->toReserve(
                    $item->product,
                    $item->quantity,
                    Reserve::TYPE_CART,
                    $this->minutes
                );
                CartStorage::find($item->id)->update(['reserve_id' => $reserve->id]);
            } else {
                $item->reserve->update(['reserve_at' => now()->addMinutes($this->minutes)]);
            }
        }


        $cart = $this->cart->getCartToFront($request['tz']);
        $preorder = 1;
        if ($request->has('preorder') && ($request->get('preorder') == "false") ) {//Очищаем корзину от излишков
            $cart['items_preorder'] = [];
            $preorder = 0;
        }

        $payments = $this->payments->get();
        $storages = $this->deliveries->storages();
        $companies = $this->deliveries->companies();

        $default = $this->service->default_user_data();

        $delivery_cost = $this->deliveries->calculate($default->delivery->user_id, $this->cart->getItems());

        return view('shop.order.create', compact('cart', 'payments',
            'storages', 'default', 'companies', 'delivery_cost', 'preorder'));
    }

    public function create_parser(Request $request)
    {
        //$cart->reload();
        $payments = $this->payments->get();
        $storages = $this->deliveries->storages();
        $companies = $this->deliveries->companies();

        $default = $this->service->default_user_data();

        $delivery_cost = $this->deliveries->calculate($default->delivery->user_id, $this->parserCart->getItems());
        $cart = $this->parserCart;
        return view('shop.order.create-parser', compact('cart', 'payments',
            'storages', 'default', 'companies', 'delivery_cost'));
    }

    public function store_parser(Request $request)
    {
        $order = $this->service->create_parser($request);
        flash('Ваш заказ успешно создан!');
        return redirect()->route('shop.order.view', $order);
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

    public function view(Request $request, Order $order)
    {

        return view('shop.order.view', compact('order'));
    }
/*
    public function index(Request $request)
    {
        $orders = Order::where('user_id', Auth::guard('user')->user()->id)->orderByDesc('updated_at')->get();
        return view('shop.order.index', compact('orders'));
    }
*/


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
