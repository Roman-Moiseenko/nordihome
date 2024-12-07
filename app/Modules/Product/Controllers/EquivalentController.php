<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\EquivalentRepository;
use App\Modules\Product\Repository\PriorityRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Service\EquivalentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;

class EquivalentController extends Controller
{

    private EquivalentService $service;
    private EquivalentRepository $repository;
    private CategoryRepository $categories;

    public function __construct(
        EquivalentService    $service,
        EquivalentRepository $repository,
        CategoryRepository   $categories,
        )
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
        $this->categories = $categories;
    }

    public function index(Request $request)
    {
        $categories = $this->categories->forFilters();
        $equivalents = $this->repository->getIndex($request, $filters);
        return Inertia::render('Product/Equivalent/Index', [
            'equivalents' => $equivalents,
            'filters' => $filters,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'integer|exists:categories,id',
        ]);
        try {
            $equivalent = $this->service->register($request);
            return redirect()->route('admin.product.equivalent.show', $equivalent)->with('success', 'Группа создана');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Equivalent $equivalent): Response
    {
        return Inertia::render('Product/Equivalent/Show', [
            'equivalent' => $this->repository->EquivalentWithToArray($equivalent),
        ]);
    }


    public function add_product(Request $request, Equivalent $equivalent): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);
        try {
            $this->service->addProductByIds($equivalent->id, (int)$request['product_id']);
            return redirect()->back()->with('success', 'Товар добавлен в группу');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(Equivalent $equivalent, Request $request): RedirectResponse
    {
        try {
            $this->service->delProductByIds($equivalent->id, $request->integer('product_id'));
            return redirect()->back()->with('success', 'Товар удален из группы');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rename(Request $request, Equivalent $equivalent): RedirectResponse
    {
        try {
            $this->service->rename($request, $equivalent);
            return redirect()->back()->with('success', 'Переименовано');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Equivalent $equivalent): RedirectResponse
    {
        try {
            $this->service->delete($equivalent);
            return redirect()->back()->with('success', 'Группа удалена');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function search(Equivalent $equivalent, Request $request): JsonResponse
    {
        try {
            $products = $this->repository->search($equivalent, $request);
            return \response()->json($products);
        } catch (\Throwable $e) {
            return \response()->json(['error' => $e->getMessage()]);
        }
    }

    //AJAX
    public function json_products(Equivalent $equivalent)
    {
        $result = [];
        foreach ($equivalent->products as $product) {
            $result[] = $product->name;
        }
        return \response()->json($result);
    }

}
