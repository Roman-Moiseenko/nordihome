<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Shop\Cart\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->middleware(['auth:user',]);
        $this->cart = $cart;
    }

    public function create(Request $request)
    {
        $cart = $this->cart->getCartToFront($request['tz']);
        $payment = $this->payments->getCurrentUser();
        $delivery = $this->deliveries->getCurrentUser();
        return view('shop.order.create', compact('cart', 'payment', 'delivery'));
    }

    public function view(Request $request)
    {
        return view('shop.order.view');
    }

    public function index(Request $request)
    {

        return view('shop.order.index');
    }
}
