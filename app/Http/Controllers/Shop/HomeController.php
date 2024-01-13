<?php

namespace App\Http\Controllers\Shop;


use App\Modules\Admin\Entity\Options;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Pages\Entity\Widget;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{

    private Options $options;

    public function __construct(Options $options)
    {
        //$this->middleware(['guest', 'guest:user']);
        //$this->middleware('auth:user');
        if (Auth::guard('admin')->check())
        {
            throw new \DomainException('^^^^');
        }
        $this->options = $options;
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $widgets = Widget::get();

        return view('shop.home', compact('widgets'));
    }

}
