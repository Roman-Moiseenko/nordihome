<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheRepository
{

    private ViewRepository $views;

    public function __construct(ViewRepository $views)
    {
        $this->views = $views;
    }

    public function product(string $slug)
    {
        return $this->views->product($slug);

      /*  return Cache::rememberForever('product-' . $slug, function () use ($slug) {
            return $this->views->product($slug);
        });*/
    }

    public function root(array $request): View
    {
        return $this->views->root($request);
    }

    public function category(array $request, string $slug): View
    {
        return $this->views->category($request, $slug);
    }

    public function page($slug): string
    {
        return $this->views->page($slug);
        //TODO Включить кеширование
/*
        return Cache::rememberForever('page-' . $slug, function () use ($slug) {
            return $this->views->page($slug);
        });*/
    }

    public function news(Request $request)
    {
        return $this->views->news($request);

    }

    public function posts($slug)
    {
        return $this->views->posts($slug);
    }

    public function post($slug)
    {
        return $this->views->post($slug);
    }
}
