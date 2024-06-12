<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ParserController extends Controller
{
    private ParserService $service;
    private ParserCart $cart;

    public function __construct(ParserService $service, ParserCart $cart)
    {
        $this->service = $service;
        $this->cart = $cart;
    }

    public function view(Request $request)
    {
        return $this->try_catch(function () use ($request) {
            $user_ui = $request->attributes->get('user_ui');

            $cart = $this->cart;
            $cart->load($user_ui);

            $title = 'Купить товары Икеа по артикулу в Калининграде и с доставкой по России';
            $description = 'Закажите товары Икеа из Польши через наш поисковый сервис. Цены ниже чем в интернет магазине';
            return view('shop.parser.show', compact('cart', 'title', 'description'));
        });
    }

    public function search(Request $request)
    {
        $user_ui = $request->attributes->get('user_ui');
        $request->validate([
            'search' => 'required|min:8'
        ]);

       return $this->try_catch(function () use ($request, $user_ui) {
            $product = $this->service->findProduct($request);
            $this->cart->load($user_ui);
            $this->cart->add($product);
            return redirect()->route('shop.parser.view');
        });
    }

    public function clear()
    {
        return $this->try_catch(function () {
            $this->cart->load();
            $this->cart->clear();
            return redirect()->route('shop.parser.view');
        });
    }

    public function remove(Product $product)
    {
        return $this->try_catch(function () use ($product) {
            $this->cart->load();
            $this->cart->remove($product);
            return redirect()->route('shop.parser.view');
        });
    }

    //AJAX
    public function add(Product $product)
    {
        return $this->try_catch_ajax(function () use ($product) {
            $this->cart->load();
            $this->cart->add($product);
            $this->cart->reload();
            return \response()->json($this->cart);
        });
    }

    public function sub(Product $product)
    {
        return $this->try_catch_ajax(function () use ($product) {
            $this->cart->load();
            $this->cart->sub($product);
            $this->cart->reload();
            return \response()->json($this->cart);
        });
    }

    public function set(Request $request, Product $product) //Product $product
    {
        return $this->try_catch_ajax(function () use ($product, $request) {
            $this->cart->load();
            $this->cart->set($product, (int)$request['quantity']);
            $this->cart->reload();
            return \response()->json($this->cart);
        });
    }

}
