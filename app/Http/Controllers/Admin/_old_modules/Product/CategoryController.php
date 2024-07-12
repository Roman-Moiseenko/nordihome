<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\_old_modules\Product;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Service\CategoryService;
use Illuminate\Http\Request;
use function redirect;
use function view;

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
        return $this->try_catch_admin(function () {
            $categories = $this->repository->getTree();
            return view('admin.product.category.index', compact('categories'));
        });
    }

    public function up(Category $category)
    {
        return $this->try_catch_admin(function () use($category) {
            $category->up();
            return redirect()->back();
        });
    }

    public function down(Category $category)
    {
        return $this->try_catch_admin(function () use($category) {
            $category->down();
            return redirect()->back();
        });
    }

    public function create(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $parents = $this->repository->withDepth();
            return view('admin.product.category.create', compact('parents'));
        });
    }

    public function child(Category $category)
    {
        return $this->try_catch_admin(function () use($category) {
            return view('admin.product.category.child', compact('category'));
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'parent' => 'nullable|integer|exists:categories,id',
        ]);
        return $this->try_catch_admin(function () use($request) {
            $category = $this->service->register($request);
            return redirect()->route('admin.product.category.show', compact('category'));
        });

    }

    public function show(Category $category)
    {
        return $this->try_catch_admin(function () use($category) {
            return view('admin.product.category.show', compact('category'));
        });
    }

    public function edit(Category $category)
    {
        return $this->try_catch_admin(function () use($category) {
            $categories = $this->repository->withDepth();
            return view('admin.product.category.edit', compact('category', 'categories'));
        });
    }

    public function update(Request $request, Category $category)
    {
        return $this->try_catch_admin(function () use($request, $category) {
            $category = $this->service->update($request, $category);
            return redirect(route('admin.product.category.show', $category));
        });
    }

    public function destroy(Category $category)
    {
        return $this->try_catch_admin(function () use($category) {
            $this->service->delete($category);
            return redirect()->back();
        });
    }

    //AJAX
    /*
    public function json_attributes(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use($request) {
            $categories_id = json_decode($request['ids']);
            $product_id = (int)$request['product_id'];
            $result = $this->repository->relationAttributes($categories_id, $product_id);
            return \response()->json($result);
        });
    }
*/
}
