<?php

namespace App\Modules\Shop\Controllers;

use App\Modules\Page\Entity\Widgets\ProductWidget;

class HomeController extends ShopController
{

    public function index()
    {
        $widgets = ProductWidget::where('active', true)->get();
        return view($this->route('home'), compact('widgets'));
    }

}
