<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Cart\Cart;
use App\Modules\User\Service\ReserveService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class CartController extends Controller
{
    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function view(Request $request)
    {
        $cart = $this->cart->getCartToFront($request['tz']);
        $cart_info = $this->cart->CommonData($cart);
        return view('shop.cart', compact('cart', 'cart_info'));
    }

    //AJAX
    public function add(Request $request, Product $product) //sub, set_count, clear
    {
        try {
            //if (!$request->has('quantity')) return;
            $this->cart->add($product, 1, $request['options'] ?? []); //(int)$request->has('quantity')
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

    }

    public function sub(Request $request, Product $product) //sub, set_count, clear
    {
        try {
            //if (!$request->has('quantity')) return;
            $this->cart->sub($product, 1); //(int)$request->has('quantity')
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }
    }

    public function set(Request $request, Product $product) //sub, set_count, clear
    {
        try {
            if (!$request->has('quantity')) return;
            $this->cart->set($product, (int)$request->get('quantity'));
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }
    }

    public function remove(Request $request, Product $product) //sub, set_count, clear
    {
        try {
            $this->cart->remove($product);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }
    }

    public function clear(Request $request) //sub, set_count, clear
    {
        try {
            $this->cart->clear();
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }
    }

    public function cart(Request $request)
    {
        try {
            (new ReserveService())->clearByTimer();
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }
    }
}
