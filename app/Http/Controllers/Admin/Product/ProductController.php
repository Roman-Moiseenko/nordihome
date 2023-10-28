<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Helper\ProductHelper;
use App\Modules\Product\Service\ProductService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
    }
    public function index(Request $request)
    {
        return view('admin.product.product.index');
    }

    public function create(Request $request)
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $menus = ProductHelper::menuAddProduct();
        return view('admin.product.product.create', compact('categories', 'menus'));
    }

    public function store(Request $request)
    {

    }

    public function show(Product $product)
    {
        return view('admin.product.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.product.product.edit', compact('product'));

    }

    public function update(Request $request, Product $product)
    {
        return view('admin.product.product.show', compact('product'));

    }

    public function destroy(Product $product)
    {

        return redirect(route('admin.product.product.index'));
    }

}
