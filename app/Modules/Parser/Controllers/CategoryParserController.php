<?php

namespace App\Modules\Parser\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Repository\CategoryParserRepository;
use App\Modules\Parser\Service\CategoryParserService;
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
        return Inertia::render('Parser/Category/Index', [
            'categories' => $categories,
        ]);
    }

    public function show(CategoryParser $category): \Inertia\Response
    {
        $product_categories = $this->categoryRepository->forFilters();
        return Inertia::render('Parser/Category/Show', [
            'category' => $category,
            'product_categories' => $product_categories,
        ]);
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
}
