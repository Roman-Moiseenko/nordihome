<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Repository\CategoryRepository;
use App\Modules\Catalog\Repository\ProductRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    private ProductRepository $products;
    private CategoryRepository $categories;

    public function __construct(
        ProductRepository $products,
        CategoryRepository $categories,
    )
    {
        $this->products = $products;
        $this->categories = $categories;
    }

    public function index(Request $request): \Inertia\Response
    {
        $categories = $this->categories->forFilters();

        $products = $this->products->getIndex($request, $filters);
        return Inertia::render('Order/Product/Index', [
            'products' => $products,
            'filters' => $filters,
            'categories' => $categories,

        ]);
    }

 /*   public function show(Product $product)
    {
        return Inertia::render('Order/Product/Show', [
            'product' => $product,
        ]);
    }*/
}
