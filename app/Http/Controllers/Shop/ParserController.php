<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

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
        return view('shop.parser.show', compact('cart'));

    }

    public function search(Request $request)
    {
        //Ищем товар, делаем расчеты и event()
        try {
            $result = $this->service->findProduct($request);
        } catch (\Throwable $e) {
            $result = [
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ];
        }
        //$this->cart->add($product);
        //Получаем новый список товаров.
        //TODO Ajax
        return \response()->json($result);
    }

    public function clear()
    {
        //Очищаем список товаров
        //TODO Ajax
        $this->cart->clear();
        return \response()->json($this->cart);
    }

    public function add(Request $request) //Product $product
    {

    }

    public function sub(Request $request) //Product $product
    {

    }


    public function order()
    {

    }


}
