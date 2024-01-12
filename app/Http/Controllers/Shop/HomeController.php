<?php

namespace App\Http\Controllers\Shop;


use App\Modules\Admin\Entity\Options;
use App\Modules\Discount\Entity\Promotion;
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

        //TODO Сделать возможность настраивать виджеты для Главной страницы
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
        $widgets = [];
        //Получаем список акций для первого виджета
        /** @var Promotion $prom */
        $prom = Promotion::where('slug', 'akciia-smakciia')->first();
        if (!is_null($prom)) {
            $items = $prom->ProductsForWidget(4);
            $item_prom[] = [
                'image' => $prom->image->getThumbUrl('promotion'),
                'url' => '', //TODO Сделать роут и Контроллер для отдельной страницы Акции
                'title' => $prom->title,

            ];

            $widgets[] = [
                'name' => '', //$prom->title,
                'widget' => 'promotions',
                'items' => array_merge($item_prom, array_map(function (Product $product) use ($prom) {
                    return [
                        'image' => $product->photo->getThumbUrl('promotion'),
                        'url' => route('shop.product.view', $product),
                        'title' => $product->getName(),
                        'price' => $product->lastPrice->value,
                        'discount' => ceil($product->lastPrice->value * ((100 - $prom->getDiscount($product->id)) / 100)),
                        'count' => $product->count_for_sell,
                    ];
                }, $items)),
            ];
        }
        //Список товаров рекомендации - Группы
        $recom = Group::find(9);
        if (!is_null($prom)) {
            $widgets[] = [
                'name' => $recom->name,
                'widget' => 'list-big',
                'items' => array_map(function (Product $product) {
                    return [
                        'image' => $product->photo->getThumbUrl('promotion'),
                        'url' => route('shop.product.view', $product),
                        'title' => $product->getName(),
                        'price' => $product->lastPrice->value,
                        'count' => $product->count_for_sell,
                    ];
                }, $recom->products()->getModels()),
            ];
        }
        //Создаем список группа - виджет
        $likvid = Group::find(10);
        if (!is_null($likvid)) {
            $widgets[] = [
                'name' => $likvid->name,
                'widget' => 'list-mini',
                'items' => array_map(function (Product $product) {
                    return [
                        'image' => $product->photo->getThumbUrl('promotion-mini'),
                        'url' => route('shop.product.view', $product),
                        'title' => $product->getName(),
                        'price' => $product->lastPrice->value,
                        'count' => $product->count_for_sell,
                    ];
                }, $likvid->products()->getModels()),
            ];

        }
        return view('shop.home', compact('widgets'));
    }

}
