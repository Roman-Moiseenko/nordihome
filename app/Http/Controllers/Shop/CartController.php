<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Cart\Cart;
use Illuminate\Http\Request;



class CartController extends Controller
{
    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function view(Request $request)
    {
        return $this->try_catch(function () use ($request) {
            $cart = $this->cart->getCartToFront($request['tz']);
            return view('shop.cart.index', compact('cart'));
        }, route('home'));

    }

    //AJAX
    public function add(Request $request, Product $product) //sub, set_count, clear
    {

        return $this->try_catch_ajax(function () use ($request, $product) {
            $this->cart->add($product, 1, $request['options'] ?? []);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
    }

    public function sub(Request $request, Product $product) //sub, set_count, clear
    {

        return $this->try_catch_ajax(function () use ($request, $product) {
            $this->cart->sub($product, 1);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
    }

    public function set(Request $request, Product $product) //sub, set_count, clear
    {
        return $this->try_catch_ajax(function () use ($request, $product) {
            if (!$request->has('quantity')) return;
            $this->cart->set($product, (int)$request->get('quantity'));
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
    }

    public function remove(Request $request, Product $product) //sub, set_count, clear
    {
        return $this->try_catch_ajax(function () use ($request, $product) {
            $this->cart->remove($product->id);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
    }

    public function clear(Request $request) //sub, set_count, clear
    {
        return $this->try_catch_ajax(function () use ($request) {
            if ($request->has('product_ids')) {
                $this->cart->removeByIds($request->get('product_ids'));
            } else {
                $this->cart->clear();
            }
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
    }

    public function cart(Request $request)
    {
        return $this->try_catch_ajax(function () use ($request) {
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
    }

    public function check(Request $request, Product $product)
    {
        return $this->try_catch_ajax(function () use ($request, $product) {
            $this->cart->check($product);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
    }

    public function check_all(Request $request)
    {
        return $this->try_catch_ajax(function () use ($request) {
            $params = json_decode($request['json'], true);
            $this->cart->check_all($params['all']);
            $cart = $this->cart->getCartToFront($params['tz']);
            return \response()->json($cart);
        });
    }
}
