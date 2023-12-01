<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Cart\Cart;
use App\Modules\User\Service\CartService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function add(Request $request, Product $product) //sub, set_count, clear
    {
        try {
            if (!$request->has('quantity')) return;
            $this->cart->add($product, (int)$request->has('quantity'), $request['options'] ?? []);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

    }

    public function sub(Request $request, Product $product) //sub, set_count, clear
    {
        try {
            if (!$request->has('quantity')) return;
            $this->cart->sub($product, (int)$request->has('quantity'));
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json($e->getMessage());
        }
    }

    public function set(Request $request, Product $product) //sub, set_count, clear
    {
        try {
            if (!$request->has('quantity')) return;
            $this->cart->set($product, (int)$request->has('quantity'));
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json($e->getMessage());
        }
    }

    public function remove(Request $request, Product $product) //sub, set_count, clear
    {
        try {
            $this->cart->remove($product);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json($e->getMessage());
        }
    }

    public function clear(Request $request) //sub, set_count, clear
    {
        try {
            $this->cart->clear();
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json($e->getMessage());
        }
    }

    public function cart(Request $request)
    {
        try {
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            return \response()->json($e->getMessage());
        }
    }
}
