<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Service\ProductService;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = Product::where('priority', true);
            $products = $this->pagination($query, $request, $pagination);
            return view('admin.product.priority.index', compact('products', 'pagination'));
        });
    }


    public function add_product(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $this->service->setPriorityProduct($request->integer('product_id'));
            return redirect()->back();
        });
    }

    public function add_products(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {

            $this->service->setPriorityProducts($request['products']);
            return redirect()->back();
        });
    }

    public function del_product(Product $product)
    {
        return $this->try_catch_admin(function () use($product) {
            $product->setPriority(false);
            return redirect()->back();
        });
    }

}
