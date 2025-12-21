<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\Shop\Parser\ParserService;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class ParserController extends ShopController
{
    private ParserService $service;
    private ParserCart $cart;
    private ViewRepository $views;

    public function __construct(ParserService $service, ParserCart $cart, ViewRepository $views)
    {
        parent::__construct();
        $this->service = $service;
        $this->cart = $cart;
        $this->views = $views;
    }

    public function index(Request $request)
    {
        return $this->views->rootParser();
    }

    public function catalog(Request $request, $slug)
    {
        return $this->views->categoryParser($request, $slug);
    }

    public function product(Request $request, $slug)
    {
        return $this->views->productParser($slug);
    }

    public function search(Request $request)
    {
        Log::info($request->string('search')->value());
        //dd($request->input('search'));
        return \response()->json(route('shop.parser.view'));
        $products = '';
        //TODO если товар один то редирект на parser.product

        //TODO если товаров больше, то страница parser.search

    }

    public function view(Request $request)
    {
        $user_ui = $request->attributes->get('user_ui');
        $cart = $this->cart;
        $cart->load($user_ui);
        $title = 'Купить товары Икеа по артикулу в Калининграде и с доставкой по России';
        $description = 'Закажите товары Икеа из Польши через наш поисковый сервис. Цены ниже чем в интернет магазине';
        return view($this->route('parser.show'), compact('cart', 'title', 'description'));
    }

/*
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
*/
}
