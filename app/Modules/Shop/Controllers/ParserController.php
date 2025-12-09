<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ParserController extends ShopController
{
    private ParserService $service;
    private ParserCart $cart;

    public function __construct(ParserService $service, ParserCart $cart)
    {
        parent::__construct();
        $this->service = $service;
        $this->cart = $cart;
    }

    public function view(Request $request)
    {
        $user_ui = $request->attributes->get('user_ui');

        $cart = $this->cart;
        //if ()
        $cart->load($user_ui);

        $title = 'Купить товары Икеа по артикулу в Калининграде и с доставкой по России';
        $description = 'Закажите товары Икеа из Польши через наш поисковый сервис. Цены ниже чем в интернет магазине';
        return view($this->route('parser.show'), compact('cart', 'title', 'description'));
    }

    public function search(Request $request)
    {
        $user_ui = $request->attributes->get('user_ui');
        $request->validate([
            'search' => 'required|min:8'
        ]);

        $product = $this->service->findProduct($request['search']);
        $this->cart->load($user_ui);
        $this->cart->add($product);
        return redirect()->route('shop.parser.view');
    }

    public function clear()
    {
        $this->cart->load();
        $this->cart->clear();
        return redirect()->route('shop.parser.view');
    }

    public function remove(Product $product)
    {
        $this->cart->load();
        $this->cart->remove($product);
        return redirect()->route('shop.parser.view');
    }

    //AJAX
    public function add(Product $product)
    {
        $this->cart->load();
        $this->cart->add($product);
        $this->cart->reload();
        return \response()->json($this->cart);
    }

    public function sub(Product $product)
    {
        $this->cart->load();
        $this->cart->sub($product);
        $this->cart->reload();
        return \response()->json($this->cart);
    }

    public function set(Request $request, Product $product) //Product $product
    {
        $this->cart->load();
        $this->cart->set($product, (int)$request->float('quantity'));
        $this->cart->reload();
        return \response()->json($this->cart);
    }

}
