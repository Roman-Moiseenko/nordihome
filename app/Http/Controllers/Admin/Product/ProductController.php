<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Tag;
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
        $brands = Brand::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.product.product.create', compact('categories', 'menus', 'brands', 'tags'));
    }

    public function store(Request $request)
    {
        $product = $this->service->create($request);
        return redirect()->route('admin.product.edit', compact('product'));
    }

    public function show(Product $product)
    {
        return view('admin.product.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $menus = ProductHelper::menuUpdateProduct();
        $brands = Brand::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $groups = AttributeGroup::orderBy('name')->get();
        return view('admin.product.product.edit', compact('product', 'categories', 'menus', 'brands', 'tags', 'groups'));

    }

    public function update(Request $request, Product $product)
    {
        $product = $this->service->update($request, $product);
        return redirect()->route('admin.product.edit', compact('product'));

    }

    public function destroy(Product $product)
    {

        return redirect(route('admin.product.product.index'));
    }

}
