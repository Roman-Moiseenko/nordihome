<?php

namespace App\Modules\Shop\Controllers;

use App\Modules\Shop\Repository\ViewRepository;

class PostController extends ShopController
{
    private ViewRepository $views;

    public function __construct(
        ViewRepository $views,
    )
    {
        parent::__construct();
        $this->views = $views;
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
