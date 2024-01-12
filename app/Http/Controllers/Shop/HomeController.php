<?php

namespace App\Http\Controllers\Shop;


use App\Modules\Admin\Entity\Options;
use App\Modules\Discount\Entity\Promotion;
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
        //Получаем список акций для первого виджета
        $prom = Promotion::where('slug', 'akciia-smakciia')->first();
        //Список товаров рекомендации - Группы


        //Создаем список группа - виджет

        $widgets = [
            [
                'name' => '',
                'widget' => 'promotions',
                'items' => [
                    [
                        'image' => '',
                        'url' => '',
                        'title' => '',
                    ]
                ],
            ],
        ];


        return view('shop.home', compact('widgets'));
    }

}
