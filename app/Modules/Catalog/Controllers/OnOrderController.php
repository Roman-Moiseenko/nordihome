<?php

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Repository\OnOrderRepository;
use App\Modules\Catalog\Service\OnOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OnOrderController extends Controller
{
    private OnOrderService $service;
    private OnOrderRepository $repository;

    public function __construct(
        OnOrderService    $service,
        OnOrderRepository $repository,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $products = $this->repository->getIndex($request, $filters);

        return Inertia::render('Catalog/OnOrder/Index', [
            'products' => $products,
            'filters' => $filters,
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
