<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

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
        $query = Product::where('priority', true);
        $products = $this->pagination($query, $request, $pagination);
        return view('admin.product.priority.index', compact('products', 'pagination'));
    }


    public function add_product(Request $request)
    {
        $this->service->setPriorityProduct($request->integer('product_id'));
        return redirect()->back();
    }

    public function add_products(Request $request)
    {
        $this->service->setPriorityProducts($request['products']);
        return redirect()->back();
    }

    public function del_product(Product $product)
    {
        $product->setPriority(false);
        return redirect()->back();
    }

}
