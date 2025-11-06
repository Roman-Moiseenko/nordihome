<?php

namespace App\Modules\Shop\Controllers;

use App\Modules\Shop\Repository\CacheRepository;
use App\Modules\Shop\Repository\ViewRepository;

class PostController extends ShopController
{
    private CacheRepository $caches;
    private ViewRepository $views;

    public function __construct(
        CacheRepository $caches,
        ViewRepository $views,
    )
    {
        parent::__construct();
        $this->caches = $caches;
        $this->views = $views;
    }

    public function posts($slug)
    {
        if ($this->web->is_cache) {
            return $this->caches->posts($slug);
        } else {
            return $this->views->posts($slug);
        }
    }

    public function post($slug)
    {
        if ($this->web->is_cache) {
            return $this->caches->post($slug);
        } else {
            return $this->views->post($slug);
        }
    }
}
