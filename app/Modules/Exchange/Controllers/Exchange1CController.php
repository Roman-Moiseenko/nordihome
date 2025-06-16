<?php

namespace App\Modules\Exchange\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Exchange\Service\Exchange1CService;
use Illuminate\Http\Request;

class Exchange1CController extends Controller
{

    private Exchange1CService $service;

    public function __construct(Exchange1CService $service)
    {
        $this->service = $service;
    }

    public function web_hook(Request $request)
    {

    }

    public function products(Request $request)
    {

        if ($this->service->authorization($request->header('authorization'))) {
            $products = $this->service->products($request);

            return response()->json($products);
        }
        return response()->json(false);
    }
}
