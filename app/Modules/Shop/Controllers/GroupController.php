<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Shop\ShopRepository;

class GroupController extends Controller
{

    private ShopRepository $repository;

    public function __construct(ShopRepository $repository)
    {
        $this->repository = $repository;
    }

    public function view(string $slug)
    {
        $group = $this->repository->getGroupBySlug($slug);
        $products = $group->products()->take(18);
        $title = 'Группа товаров ' . $group->name . ' | Цены снижены в интернет-магазине';
        $description = $group->description;
        return view('shop.group', compact('group', 'products', 'title', 'description'));
    }


    public function index()
    {
        //TODO Список действующих акция ?????
    }
}
