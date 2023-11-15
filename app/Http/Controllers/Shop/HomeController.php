<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{

    public function __construct()
    {
        //$this->middleware(['guest', 'guest:user']);
        //$this->middleware('auth:user');
        if (Auth::guard('admin')->check())
        {
            throw new \DomainException('^^^^');
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('shop.home');
    }
}
