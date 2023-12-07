<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    public function begin()
    {
        //Проверяем залогинен клиент
        //Если да, то редирект на order.view
        //Иначе на страницу заполнения данных order.login
    }
}
