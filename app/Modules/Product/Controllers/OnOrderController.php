<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\OnOrderRepository;
use App\Modules\Product\Repository\ReducedRepository;
use App\Modules\Product\Service\OnOrderService;
use App\Modules\Product\Service\ReducedService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OnOrderController extends Controller
{
    private OnOrderService $service;
    private OnOrderRepository $repository;
    private CategoryRepository $categories;

    public function __construct(
        OnOrderService    $service,
        OnOrderRepository $repository,
        CategoryRepository $categories,
    )
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
        $this->categories = $categories;
    }

    public function index(Request $request): Response
    {
        $products = $this->repository->getIndex($request, $filters);
        $categories = $this->categories->forFilters();

        return Inertia::render('Product/OnOrder/Index', [
            'products' => $products,
            'filters' => $filters,
            'categories' => $categories,
        ]);
    }


    public function add_product(Request $request): RedirectResponse
    {
        $this->service->setOnOrderProduct($request->integer('product_id'));
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(Request $request): RedirectResponse
    {
        $this->service->setOnOrderProducts($request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    public function del_product(Product $product): RedirectResponse
    {
        $this->service->delOnOrderProduct($product);
        return redirect()->back()->with('success', 'Товар убран');
    }
}
