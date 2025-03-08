<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

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

    public function root(array $request)
    {
        return $this->views->root($request);
    }

    public function category(array $request, string $slug)
    {
        return $this->views->category($request, $slug);
    }

    public function page($slug)
    {
        return $this->views->page($slug);
/*
        return Cache::rememberForever('page-' . $slug, function () use ($slug) {
            return $this->views->page($slug);
        });*/
    }
}
