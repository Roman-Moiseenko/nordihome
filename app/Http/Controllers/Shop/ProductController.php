<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{

    private ShopRepository $repository;

    public function __construct(ShopRepository $repository)
    {
        $this->repository = $repository;
    }

    public function view($slug)
    {
        try {
            $product = $this->repository->getProductBySlug($slug);

            $title = $product->name . ' купить по цене ' . $product->lastPrice->value . '₽ ☛ Доставка по всей России ★★★ Интернет-магазин Норди Хоум Калининград';
            $description = $product->short;

            return view('shop.product', compact('product', 'title', 'description'));
        } catch (\Throwable $e) {
            $product = null;
            flash($e->getMessage(), 'danger');
            return redirect()->route('home'); //'error.403', ['message' => $e->getMessage()]
        }
    }

    public function search(Request $request)
    {
        if (empty($request['search'])) return ;

        try {
            $result = $this->repository->search($request['search']);
        } catch (\Throwable $e) {
            $result = ['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]];
        }
        return \response()->json($result);
    }

}
