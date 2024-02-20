<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

    public function view()
    {
        //Загружаем товары посетителя из Хранилища Storage_Parser
        $cart = $this->cart;
        $cart->load();
        //TODO Заглушка - для нового посетителя, с точкой входа - Парсер
        if (!Auth::guard('user')->check() && empty(Cookie::get('user_cookie_id')))
            return redirect()->route('shop.parser.view');
        $title = 'Купить товары Икеа по артикулу в Калининграде и с доставкой по России';
        $description = 'Закажите товары Икеа из Польши через наш поисковый сервис. Цены ниже чем в интернет магазине';
        return view('shop.parser.show', compact('cart', 'title', 'description'));
    }


    public function search(Request $request)
    {
        //Ищем товар, делаем расчеты и event()
        $request->validate([
            'search' => 'required|min:8'
        ]);
        try {
            $product = $this->service->findProduct($request);
            $this->cart->load();
            $this->cart->add($product);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        catch (\Throwable $e) {
            flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'info');
            event(new ThrowableHasAppeared($e));
        }
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
            $this->cart->set($product, (int)$request['quantity']);
            $this->cart->reload();
            return \response()->json($this->cart);
        } catch (\Throwable $e) {
            return \response()->json([$e->getFile(), $e->getLine(), $e->getMessage()]);
        }
    }

}
