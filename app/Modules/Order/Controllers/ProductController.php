<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Repository\ProductRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    private ProductRepository $products;

    public function __construct(
        ProductRepository $products,
    )
    {
        $this->products = $products;
    }

    public function index(Request $request): \Inertia\Response
    {
        $products = $this->products->getIndex($request, $filters);
        return Inertia::render('Order/Product/Index', [
            'products' => $products,
            'filters' => $filters,

        ]);
    }

 /*   public function show(Product $product)
    {
        return Inertia::render('Order/Product/Show', [
            'product' => $product,
        ]);
    }*/
}
