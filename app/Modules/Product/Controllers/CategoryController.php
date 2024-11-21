<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Service\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function index()
    {
        $categories = $this->repository->getTree();
        return view('admin.product.category.index', compact('categories'));
    }

    public function up(Category $category)
    {
        $category->up();
        return redirect()->back();
    }

    public function down(Category $category)
    {
        $category->down();
        return redirect()->back();
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
            'parent' => 'nullable|integer|exists:categories,id',
        ]);
        $category = $this->service->register($request);
        return redirect()->route('admin.product.category.show', compact('category'));
    }

    public function show(Category $category)
    {
        return view('admin.product.category.show', compact('category'));
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
        $categories = array_map(function (Category $category){

            $depth = str_repeat('-', $category->depth);
            return [
                'id' => $category->id,
                'name' => $depth . $category->name,
            ];
        }, $this->repository->withDepth());
      /*  $list = Brand::orderBy('name')->get()->map(function (Brand $brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
            ];
        });*/
        return response()->json($categories);
    }

}
