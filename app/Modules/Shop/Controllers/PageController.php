<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Page\Entity\Page;
use App\Modules\Shop\Repository\CacheRepository;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Http\Request;
use Cache;


class PageController extends ShopController
{

    private ShopRepository $repository;
    private CacheRepository $caches;
    private ViewRepository $views;

    public function __construct(ShopRepository $repository,
                                CacheRepository $caches,
                                ViewRepository $views,
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->caches = $caches;
        $this->views = $views;
    }

    public function home()
    {

        if (!is_null(Page::where('slug', 'home')->active()->first())) {
            $callback = fn() => $this->views->page('home'); //$page->view();
        } else {
            $callback = fn() => view($this->route('home'))->render();
        }

        //TODO Настройка использовать кеширование
        //return $callback();
        if ($this->web->is_cache) {

            return Cache::rememberForever('home', $callback);
        } else {
            return $callback();
        }
    }

    public function view($slug)
    {
        if ($this->web->is_cache) {
            return $this->caches->page($slug);
        } else {
            return $this->views->page($slug);
        }
    }

    public function news(Request $request)
    {
        if ($this->web->is_cache) {
            return $this->caches->news($request);
        } else {
            return $this->views->news($request);
        }
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
