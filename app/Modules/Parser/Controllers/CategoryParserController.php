<?php

namespace App\Modules\Parser\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Repository\CategoryParserRepository;
use App\Modules\Parser\Service\CategoryParserService;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Repository\CategoryRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryParserController extends Controller
{
    private CategoryParserRepository $repository;
    private CategoryParserService $service;
    private CategoryRepository $categoryRepository;

    public function __construct(
        CategoryParserRepository $repository,
        CategoryParserService    $service,
        CategoryRepository $categoryRepository
    )
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->repository = $repository;
        $this->service = $service;
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): \Inertia\Response
    {
        $categories = $this->repository->getTree();
        $product_categories = $this->categoryRepository->forFilters();
        return Inertia::render('Parser/Category/Index', [
            'categories' => $categories,
            'brands' => Brand::where('parser_class', '<>', null)->getModels(),
            'product_categories' => $product_categories,
        ]);
    }

    public function show(CategoryParser $category): \Inertia\Response
    {
        $product_categories = $this->categoryRepository->forFilters();
        return Inertia::render('Parser/Category/Show', [
            'category' => $this->repository->CategoryWithToArray($category),
            'product_categories' => $product_categories,
        ]);
    }
    public function set_category(CategoryParser $category, Request $request)
    {
        $this->service->setCategory($category, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function toggle(CategoryParser $category): RedirectResponse
    {
        if ($category->active) {
            $category->draft();
            return redirect()->back()->with('success', 'Категория убрана из парсинга');
        } else {
            $category->active();
            return redirect()->back()->with('success', 'Категория добавлена в парсинг');
        }
    }

    public function parser_products(CategoryParser $category): \Illuminate\Http\JsonResponse
    {
        $products = $this->service->parserProducts($category);
        return response()->json($products);// redirect()->back()->with('success', 'Спарсено');
    }


    public function parser_product(CategoryParser $category, Request $request)
    {
        try {
            $this->service->parserProduct($category, $request);
           // return redirect()->back();
            return response()->json(true);
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

    }

    public function add_category(Request $request): RedirectResponse
    {
        $this->service->addCategory($request);
        return redirect()->back()->with('success', 'Добавлено');

    }

    public function destroy(CategoryParser $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Удалено');
    }
}
