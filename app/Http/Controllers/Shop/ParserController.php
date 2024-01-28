<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ParserController extends Controller
{
    private ParserService $service;
    private ParserCart $cart;

    public function __construct(ParserService $service, ParserCart $cart)
    {
        $this->service = $service;
        $this->cart = $cart;
    }

    public function view()
    {
        //Загружаем товары посетителя из Хранилища Storage_Parser
        $cart = $this->cart;
        $cart->load();
        return view('shop.parser.show', compact('cart'));
    }


    public function search(Request $request)
    {
        //Ищем товар, делаем расчеты и event()
        try {
            $product = $this->service->findProduct($request);
            $this->cart->load();
            $this->cart->add($product);
        } catch (\Throwable $e) {
            flash($e->getMessage());
        }
        return redirect()->route('shop.parser.view');
    }


    public function clear()
    {
        //Очищаем список товаров
        //TODO Ajax
        $this->cart->load();
        $this->cart->clear();
        return redirect()->route('shop.parser.view');

        //return \response()->json($this->cart);
    }

    public function remove(Product $product) //Product $product
    {
        try {
            $this->cart->load();
            $this->cart->remove($product);
        } catch (\Throwable $e) {
            flash($e->getMessage());
        }
        return redirect()->route('shop.parser.view');

    }

    //AJAX

    public function add(Product $product)
    {
        try {
            $this->cart->load();
            $this->cart->add($product);
            $this->cart->reload();
            return \response()->json($this->cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getFile(), $e->getLine(), $e->getMessage()]);
        }
    }

    public function sub(Product $product)
    {
        try {
            $this->cart->load();
            $this->cart->sub($product);
            $this->cart->reload();
            return \response()->json($this->cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getFile(), $e->getLine(), $e->getMessage()]);
        }
    }

    public function set(Request $request, Product $product) //Product $product
    {
        try {
            $this->cart->load();
            $this->cart->set($product, $request['quantity']);
            $this->cart->reload();
            return \response()->json($this->cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getFile(), $e->getLine(), $e->getMessage()]);
        }
    }

}
