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
use Inertia\Response;

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

    public function index(): Response
    {
        $categories = $this->repository->getTree();
        //$product_categories = $this->categoryRepository->forFilters();
        return Inertia::render('Parser/Category/Index', [
            'categories' => $categories,
            'brands' => Brand::where('parser_class', '<>', null)->getModels(),
        //    'product_categories' => $product_categories,
        ]);
    }

    public function show(CategoryParser $category_parser): Response
    {
        $product_categories = $this->categoryRepository->forFilters();
        return Inertia::render('Parser/Category/Show', [
            'category' => $this->repository->CategoryWithToArray($category_parser),
            'product_categories' => $product_categories,
        ]);
    }
    public function set_category(CategoryParser $category_parser, Request $request): RedirectResponse
    {
        $this->service->setCategory($category_parser, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function toggle(CategoryParser $category_parser): RedirectResponse
    {
        $message = $this->service->toggle($category_parser);

        return redirect()->back()->with('success', $message);
    }

    public function parser_products(CategoryParser $category_parser): \Illuminate\Http\JsonResponse
    {
        $products = $this->service->parserProducts($category_parser);
        return response()->json($products);// redirect()->back()->with('success', 'Спарсено');
    }


    public function parser_product(CategoryParser $category_parser, Request $request): \Illuminate\Http\JsonResponse
    {
            $this->service->parserProduct($category_parser, $request);
            return response()->json(true);
    }

    public function add_category(Request $request): RedirectResponse
    {
        $this->service->addCategory($request);
        return redirect()->back()->with('success', 'Добавлено');

    }

    public function destroy(CategoryParser $category_parser): RedirectResponse
    {
        $category_parser->delete();
        return redirect()->back()->with('success', 'Удалено');
    }
}
