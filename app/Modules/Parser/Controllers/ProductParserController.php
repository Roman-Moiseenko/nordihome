<?php

namespace App\Modules\Parser\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Repository\ProductParserRepository;
use App\Modules\Parser\Service\ProductParserService;
use Illuminate\Http\Request;

class ProductParserController extends Controller
{

    private ProductParserService $service;
    private ProductParserRepository $repository;

    public function __construct(
        ProductParserService $service,
        ProductParserRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        //TODO
    }


    public function parser(ProductParser $product): \Illuminate\Http\RedirectResponse
    {
        $price = $this->service->parserProduct($product);
        return redirect()->back()->with('success', 'Товар спарсен: ' . $price);
    }

    public function by_list(Request $request): \Illuminate\Http\RedirectResponse
    {
        //dd($request);

      //  $this->service->parserProducts($request);
      //  return redirect()->back()->with('success', 'Товары добавлены в очередь на спарсивание');
    }
}
