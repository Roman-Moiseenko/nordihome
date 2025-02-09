<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;

class PromotionController extends ShopController
{

    private ShopRepository $repository;
    private SlugRepository $slugs;

    public function __construct(ShopRepository $repository, SlugRepository $slugs)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->slugs = $slugs;
    }

    public function view(string $slug)
    {
        $promotion = $this->slugs->getPromotionBySlug($slug);
        $products = $promotion->products;
        $title = 'Акция ' . $promotion->title . ' | Цены снижены в интернет-магазине';
        $description = $promotion->description;
        return view($this->route('promotion'), compact('promotion', 'products', 'title', 'description'));
    }


    public function index()
    {
        //TODO Список действующих акция ?????
    }
}
