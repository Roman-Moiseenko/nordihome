<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
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
        try {
            $categories = $this->repository->getTree();
            return view('admin.product.category.index', compact('categories'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function up(Category $category)
    {
        try {
            $category->up();
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function down(Category $category)
    {
        try {
            $category->down();
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create(Request $request)
    {
        try {
            $parents = $this->repository->withDepth();
            return view('admin.product.category.create', compact('parents'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function child(Category $category)
    {
        try {
            return view('admin.product.category.child', compact('category'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'parent' => 'nullable|integer|exists:categories,id',
        ]);
        try {
            $category = $this->service->register($request);
            return redirect()->route('admin.product.category.show', compact('category'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Category $category)
    {
        try {
            return view('admin.product.category.show', compact('category'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Category $category)
    {
        try {
            $categories = $this->repository->withDepth();
            return view('admin.product.category.edit', compact('category', 'categories'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, Category $category)
    {
        try {
            $category = $this->service->update($request, $category);
            return redirect(route('admin.product.category.show', $category));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Category $category)
    {
        try {
            $this->service->delete($category);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function json_attributes(Request $request)
    {
        try {
        $categories_id = json_decode($request['ids']);
        $product_id = (int)$request['product_id'];
        $result = $this->repository->relationAttributes($categories_id, $product_id);
        return \response()->json($result);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
            return \response()->json(['error' => $e->getMessage()]);
        }
    }

}
