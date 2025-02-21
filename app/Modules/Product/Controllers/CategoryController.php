<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Service\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\Deprecated;

class CategoryController extends Controller
{

    private CategoryService $service;
    private CategoryRepository $repository;

    public function __construct(CategoryService $service, CategoryRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(): Response
    {
        $categories = $this->repository->getTree();
        return Inertia::render('Product/Category/Index', [
            'categories' => $categories,
        ]);
    }

    public function show(Category $category): Response
    {
        $categories = $this->repository->forFilters();
        return Inertia::render('Product/Category/Show', [
            'category' => $this->repository->CategoryWith($category),
            'categories' => $categories,
        ]);
    }

    public function set_info(Category $category, Request $request): RedirectResponse
    {
        $this->service->setInfo($request, $category);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function up(Category $category): RedirectResponse
    {
        $category->up();
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down(Category $category): RedirectResponse
    {
        $category->down();
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function create(Request $request)
    {
        $parents = $this->repository->withDepth();
        return view('admin.product.category.create', compact('parents'));
    }

    public function child(Category $category)
    {
        return view('admin.product.category.child', compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|integer|exists:categories,id',
        ]);
        $category = $this->service->register($request);
        return redirect()->route('admin.product.category.show', $category)->with('success', 'Категория создана');
    }


    public function edit(Category $category)
    {
        $categories = $this->repository->withDepth();
        return view('admin.product.category.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $category = $this->service->update($request, $category);
        return redirect(route('admin.product.category.show', $category));
    }

    public function destroy(Category $category)
    {
        $this->service->delete($category);
        return redirect()->back();
    }

    public function list(): JsonResponse
    {
        $categories = array_map(function (Category $category) {

            $depth = str_repeat('-', $category->depth);
            return [
                'id' => $category->id,
                'name' => $depth . $category->name,
            ];
        }, $this->repository->withDepth());

        return response()->json($categories);
    }

}
