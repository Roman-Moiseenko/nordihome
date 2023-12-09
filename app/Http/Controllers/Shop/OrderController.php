<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Delivery\Service\DeliveryService;
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

    public function __construct(Cart $cart, PaymentService $payments, DeliveryService $deliveries)
    {
        $this->middleware(['auth:user']);
        $this->cart = $cart;
        $this->payments = $payments;
        $this->deliveries = $deliveries;
    }

    public function create(Request $request)
    {
        $user_id = Auth::guard('user')->user()->id;

        $cart = $this->cart->getCartToFront($request['tz']);
        $payments = $this->payments->get($user_id);
        $storages = $this->deliveries->storages();
        $companies = $this->deliveries->companies();
        //$deliveries = $this->deliveries->get($user_id);
        $default = [
            'payment' => [
                'class' => '',
            ],
            'delivery' => [
                'company' => '',
                'type' => '',
                'storage' => '',
                'address' => '',
                'post' => ''
            ],
        ];
        return view('shop.order.create', compact('cart', 'payments', 'storages', 'default', 'companies'));
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
    public function payment(Request $request)
    {
        try {


            $isOnline = $this->payments->online($request['class']);
            $invoice = '';
            if (!$isOnline) {
                $invoice = $this->payments->invoice($request['class']);
            }
            $result = [
                'online' => $isOnline,
                'invoice' => $invoice,
            ];
        } catch (\Throwable $e) {
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
        }
        return \response()->json($result);
    }
    ///
}
