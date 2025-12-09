<?php

namespace App\Modules\Parser\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Repository\CategoryParserRepository;
use App\Modules\Parser\Repository\ProductParserRepository;
use App\Modules\Parser\Service\ProductParserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductParserController extends Controller
{

    private ProductParserService $service;
    private ProductParserRepository $repository;
    private CategoryParserRepository $categories;

    public function __construct(
        ProductParserService $service,
        ProductParserRepository $repository,
        CategoryParserRepository $categories
    )
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
        $this->categories = $categories;
    }

    public function index(Request $request): \Inertia\Response
    {
        $categories = $this->categories->forFilters();
        $products = $this->repository->getIndex($request, $filters);
        return Inertia::render('Parser/Product/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,

        ]);
    }

    public function show(ProductParser $product_parser)
    {
        return Inertia::render('Parser/Product/Show', [
            'products' => $this->repository->ProductWithToArray($product_parser),
        ]);
    }

    public function available(ProductParser $product_parser): \Illuminate\Http\RedirectResponse
    {
        $message = $this->service->available($product_parser);
        return redirect()->back()->with('success', $message);
    }

    public function fragile(ProductParser $product_parser): \Illuminate\Http\RedirectResponse
    {
        $message = $this->service->fragile($product_parser);
        return redirect()->back()->with('success', $message);
    }

    public function sanctioned(ProductParser $product_parser): \Illuminate\Http\RedirectResponse
    {
        $message = $this->service->sanctioned($product_parser);
        return redirect()->back()->with('success', $message);
    }

    public function parser(ProductParser $product_parser): \Illuminate\Http\RedirectResponse
    {
        $price = $this->service->parserProduct($product_parser);
        return redirect()->back()->with('success', 'Товар спарсен: ' . $price);
    }

    public function by_list(Request $request): \Illuminate\Http\RedirectResponse
    {
        //dd($request);

      //  $this->service->parserProducts($request);
      //  return redirect()->back()->with('success', 'Товары добавлены в очередь на спарсивание');
    }

}
