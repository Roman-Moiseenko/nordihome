<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Shop\ShopRepository;
use Illuminate\Routing\Controller;

class PromotionController extends Controller
{

    private ShopRepository $repository;

    public function __construct(ShopRepository $repository)
    {
        $this->repository = $repository;
    }

    public function view(string $slug)
    {
        $promotion = $this->repository->getPromotionBySlug($slug);
        $products = $promotion->products();
        $title = 'Акция ' . $promotion->title . ' | Цены снижены в интернет-магазине';
        $description = $promotion->description;
        return view('shop.promotion', compact('promotion', 'products', 'title', 'description'));
    }


    public function index()
    {
        //TODO Список действующих акция ?????
    }
}
