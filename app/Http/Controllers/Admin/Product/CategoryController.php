<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Service\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{

    private CategoryService $service;
    private CategoryRepository $repository;

    public function __construct(CategoryService $service, CategoryRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
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
        return back();
    }

    public function down(Category $category)
    {
        $category->down();
        return back();
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
        //return view('admin.product.category.show', compact('category'));
    }

    public function destroy(Category $category)
    {
        try {
            $this->service->delete($category);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return back();
    }

    public function json_attributes(Request $request)
    {
        $categories_id = json_decode($request['ids']);
        $product_id = (int)$request['product_id'];
        $result = $this->repository->relationAttributes($categories_id, $product_id);
        return \response()->json($result);
    }

}
