<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Content\Entity\Page;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Http\Request;
use Cache;


class PageController extends ShopController
{

    private ShopRepository $repository;
    private ViewRepository $views;

    public function __construct(ShopRepository $repository,
                                ViewRepository $views,
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->views = $views;
    }

    public function home()
    {

        if (!is_null(Page::where('slug', 'home')->active()->first())) {

            $callback = fn() => $this->views->page('home'); //$page->view();
        } else {
            $callback = fn() => view($this->route('home'))->render();
        }
        //return $this->caches->page('home');
        return $callback();

    }

    public function view($slug)
    {

            return $this->views->page($slug);

    }

    public function news(Request $request)
    {

            return $this->views->news($request);

    }

    public function map_data(Request $request)
    {
        $map = $this->repository->getMapData($request);
        return response()->json($map);
    }

    public function email(Request $request)
    {
        abort(404);
        //TODO Обратная связь
        // Необходимо сохранение писем в базе и передача их на Исполнение
        //Mail::to($request['email'])->queue(new FeedBack($request['email'], $request['phone'], $request['message']));
        //return redirect()->back();
    }
}
