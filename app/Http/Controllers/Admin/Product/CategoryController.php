<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Service\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class CategoryController extends Controller
{


    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
    }

    public function index()
    {
        $categories = Category::defaultOrder()->get()->toTree(); //withDepth()->get();
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
        $parents = Category::defaultOrder()->withDepth()->get();
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
        $categories = Category::defaultOrder()->withDepth()->get();
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


}
