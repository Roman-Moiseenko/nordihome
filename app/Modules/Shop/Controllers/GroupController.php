<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Shop\ShopRepository;

class GroupController extends ShopController
{

    private ShopRepository $repository;

    public function __construct(ShopRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function view(string $slug)
    {
        $group = $this->repository->getGroupBySlug($slug);
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
