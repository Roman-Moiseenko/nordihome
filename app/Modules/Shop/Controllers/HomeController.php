<?php

namespace App\Modules\Shop\Controllers;

use App\Modules\Page\Entity\ProductWidget;

class HomeController extends ShopController
{

    public function index()
    {
        //dd("3");
        $widgets = ProductWidget::where('active', true)->get();
        return view($this->route('home'), compact('widgets'));
    }

}
