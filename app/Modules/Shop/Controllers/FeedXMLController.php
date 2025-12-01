<?php

namespace App\Modules\Shop\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Unload\Entity\Feed;
use App\Modules\Unload\Repository\FeedRepository;

class FeedXMLController extends Controller
{
    private FeedRepository $repository;

    public function __construct(FeedRepository $repository)
    {
        $this->repository = $repository;
    }

    public function google(Feed $feed)
    {
        if (!$feed->active) abort(404);
        $date = now()->addDays(14)->format('Y-m-d\TH:i+0200'); //2016-12-25T13:00-0800
        $products = $this->repository->GetProducts($feed);
        $info = $this->repository->getInfo($feed);
        $content = view('shop.unload.feed-google', compact('products', 'info', 'date'))->render();
        ob_end_clean();
        return response($content)->header('Content-Type','text/xml');
    }

    public function yandex(Feed $feed)
    {
        if (!$feed->active) abort(404);
        $date = now()->format('Y-m-d\TH:i');
        $products = $this->repository->GetProducts($feed);
        $yml_categories = $this->repository->GetCategories($products);
        $info = $this->repository->getInfo($feed);
        $content = view('shop.unload.feed-yandex', compact('products', 'info', 'yml_categories', 'date'))->render();
        ob_end_clean();
        return response($content)->header('Content-Type','text/xml');
    }
}
