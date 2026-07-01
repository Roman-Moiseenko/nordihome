<?php

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Entity\Product;
use App\Modules\Catalog\Repository\CategoryRepository;
use App\Modules\Catalog\Repository\ReducedRepository;
use App\Modules\Catalog\Service\ReducedService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReducedController extends Controller
{
    private ReducedService $service;
    private ReducedRepository $repository;
    private CategoryRepository $categories;

    public function __construct(
        ReducedService    $service,
        ReducedRepository $repository,
        CategoryRepository $categories,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->categories = $categories;
    }

    public function index(Request $request): Response
    {
        $products = $this->repository->getIndex($request, $filters);
        return Inertia::render('Catalog/Reduced/Index', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }


    public function add_product(Request $request): RedirectResponse
    {
        $this->service->setReducedProduct($request->integer('product_id'));
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(Request $request): RedirectResponse
    {
        $this->service->setReducedProducts($request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    public function del_product(Product $product): RedirectResponse
    {
        $this->service->delReducedProduct($product);
        return redirect()->back()->with('success', 'Товар убран');
    }
}
