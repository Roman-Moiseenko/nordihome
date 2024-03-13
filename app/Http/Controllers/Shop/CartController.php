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
       /* try {
            $cart = $this->cart->getCartToFront($request['tz']);
            return view('shop.cart.index', compact('cart'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        catch (\Throwable $e) {
            flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'info');
            event(new ThrowableHasAppeared($e));
        }
        return redirect()->route('home');*/

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
/*
        try {
            $this->cart->add($product, 1, $request['options'] ?? []);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\DomainException $e){
            return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
            } else {
                event(new ThrowableHasAppeared($e));
                return \response()->json(false);
            }
        }
*/
    }

    public function sub(Request $request, Product $product) //sub, set_count, clear
    {

        return $this->try_catch_ajax(function () use ($request, $product) {
            $this->cart->sub($product, 1);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });

       /* try {
            $this->cart->sub($product, 1);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }*/
    }

    public function set(Request $request, Product $product) //sub, set_count, clear
    {
        return $this->try_catch_ajax(function () use ($request, $product) {
            if (!$request->has('quantity')) return;
            $this->cart->set($product, (int)$request->get('quantity'));
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });

     /*   try {
            if (!$request->has('quantity')) return;
            $this->cart->set($product, (int)$request->get('quantity'));
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }*/
    }

    public function remove(Request $request, Product $product) //sub, set_count, clear
    {
        return $this->try_catch_ajax(function () use ($request, $product) {
            $this->cart->remove($product);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
        /*
        try {
            $this->cart->remove($product);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }*/
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

      /*  try {
            if ($request->has('product_ids')) {
                $this->cart->removeByIds($request->get('product_ids'));
            } else {
                $this->cart->clear();
            }
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }*/
    }

    public function cart(Request $request)
    {
        return $this->try_catch_ajax(function () use ($request) {
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
        /*
        try {
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }*/
    }

    public function check(Request $request, Product $product)
    {
        return $this->try_catch_ajax(function () use ($request, $product) {
            $this->cart->check($product);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        });
        /*
        try {
            $this->cart->check($product);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }*/
    }
    public function check_all(Request $request)
    {
        return $this->try_catch_ajax(function () use ($request) {
            $params = json_decode($request['json'], true);
            $this->cart->check_all($params['all']);
            $cart = $this->cart->getCartToFront($params['tz']);
            return \response()->json($cart);
        });
      /*
        try {
            $params = json_decode($request['json'], true);
            $this->cart->check_all($params['all']);
            $cart = $this->cart->getCartToFront($params['tz']);
            return \response()->json($cart);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }*/
    }
}
