<?php

namespace App\Modules\Exchange\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Exchange1CController extends Controller
{

    public function web_hook(Request $request)
    {

    }

    public function products(Request $request)
    {
        dd($request->header());
    }
}
