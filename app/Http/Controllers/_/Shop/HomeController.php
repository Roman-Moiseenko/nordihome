<?php

namespace App\Http\Controllers\_\Shop;


use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Options;
use App\Modules\Page\Entity\Widget;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use function event;
use function flash;
use function view;

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
