<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ParserController extends Controller
{
    public function view()
    {
        //Загружаем товары посетителя из Хранилища Storage_Parser
        $products = [];
        return view('shop.parser.show', compact('products'));
    }

    public function search(Request $request)
    {
        //Ищем товар, делаем расчеты и event()

        //Получаем новый список товаров.
        //TODO Ajax
    }

    public function clear()
    {
        //Очищаем список товаров
        //TODO Ajax
    }


    public function order()
    {

    }


}
