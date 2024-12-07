<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\PriorityRepository;
use App\Modules\Product\Service\PriorityService;
use App\Modules\Product\Service\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PriorityController extends Controller
{
    private PriorityService $service;
    private PriorityRepository $repository;
    private CategoryRepository $categories;

    public function __construct(
        PriorityService    $service,
        PriorityRepository $repository,
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

        return Inertia::render('Product/Priority/Index', [
            'products' => $products,
            'filters' => $filters,
            'categories' => $categories,
        ]);
    }


    public function add_product(Request $request): RedirectResponse
    {
        try {
            $this->service->setPriorityProduct($request->integer('product_id'));
            return redirect()->back()->with('success', 'Товар добавлен в приоритет');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_products(Request $request): RedirectResponse
    {
        try {
            $this->service->setPriorityProducts($request['products']);
            return redirect()->back()->with('success', 'Товары добавлены в приоритет');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(Product $product): RedirectResponse
    {
        try {
            $this->service->delPriorityProduct($product);
            return redirect()->back()->with('success', 'Товар убран из приоритета');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
