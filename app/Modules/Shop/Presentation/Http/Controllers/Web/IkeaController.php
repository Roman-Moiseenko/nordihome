<?php

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

class IkeaController
{

    public function __construct()
    {

    }
    public function index()
    {
        $ikeaCategories = [];
        return view('shop.ikea.index', ['ikeaCategories' => $ikeaCategories]);
    }
}
