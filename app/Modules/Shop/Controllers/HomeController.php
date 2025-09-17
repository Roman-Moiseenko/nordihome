<?php

namespace App\Modules\Shop\Controllers;

use App\Modules\Page\Entity\Widget;

class HomeController extends ShopController
{

    public function index()
    {
        //dd("3");
        $widgets = Widget::where('active', true)->get();
        return view($this->route('home'), compact('widgets'));
    }

}
