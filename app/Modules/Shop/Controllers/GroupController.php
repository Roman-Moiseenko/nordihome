<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;

class GroupController extends ShopController
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
        $group = $this->slugs->getGroupBySlug($slug);
        $products = $group->products;
        $title = 'Группа товаров ' . $group->name . ' | Цены снижены в интернет-магазине';
        $description = $group->description;
        return view($this->route('group'), compact('group', 'products', 'title', 'description'));
    }


    public function index()
    {
        //TODO Список действующих акция ?????
    }
}
