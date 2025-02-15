<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Page\Entity\Page;
use App\Modules\Shop\Repository\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends ShopController
{

    private ShopRepository $repository;

    public function __construct(ShopRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function home()
    {
        if (!is_null($page = Page::where('slug', 'home')->active()->first())) {
            $callback = fn() => $page->view();
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
        try {
            $page = Page::where('slug', $slug)->where('published', true)->firstOrFail();
            $callback = fn() => $page->view();

            //if ($this->web->is_cache) {
            //    return Cache::rememberForever('page-' . $slug, $callback);
         //   } else {
                return $callback();
         //   }

        } catch (\Throwable $e) {
            abort(404, 'Страница не найдена');
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
