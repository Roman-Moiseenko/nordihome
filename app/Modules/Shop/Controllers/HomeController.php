<?php

namespace App\Modules\Shop\Controllers;


use App\Events\ThrowableHasAppeared;
use App\Modules\Admin\Entity\Options;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\Widget;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{

    private Options $options;

    public function __construct(Options $options)
    {
        //$this->middleware(['guest', 'guest:user']);
        //$this->middleware('auth:user');
        if (Auth::guard('admin')->check()) {
            Auth::logout();
            //throw new \DomainException('^^^^');
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
        $widgets = [];
        try {
            //TODO После исправления акций включить
            $widgets = Widget::where('active', true)->get();
        } catch (\Throwable $e) {
            flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'info');
            event(new ThrowableHasAppeared($e));
        }
        return view('shop.home', compact('widgets'));
    }

}
