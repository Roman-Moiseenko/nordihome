<?php

namespace App\Modules\Parser\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Application\Actions\Product\IndexParserProductUseCase;
use App\Modules\Parser\Application\DTOs\Product\ParserProductFilterData;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Parser\Repository\ProductParserRepository;
use App\Modules\Parser\Service\ProductParserService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductParserController extends Controller
{

    private ProductParserService $service;

    public function __construct(
        ProductParserService $service,
        private readonly IndexParserProductUseCase $indexParserProductUseCase,
    )
    {
        $this->service = $service;
    }

    public function index(Request $request, UserPermission $userPermission): \Inertia\Response
    {
        $dto = ParserProductFilterData::validateAndCreate($request->all());
        $products = $this->indexParserProductUseCase->execute($dto, $userPermission);
      //  $products = $this->repository->getIndex($request, $filters);
        return Inertia::render('Parser/Product/Index', [
            'products' => $products,
            'filters' => $dto,

        ]);
    }

    public function show(ParserProduct $product_parser)
    {
        return Inertia::render('Parser/Product/Show', [
          //  'products' => $this->repository->ProductWithToArray($product_parser),
        ]);
    }

    public function available(ParserProduct $product_parser): \Illuminate\Http\RedirectResponse
    {
        $message = $this->service->available($product_parser);
        return redirect()->back()->with('success', $message);
    }

    public function fragile(ParserProduct $product_parser): \Illuminate\Http\RedirectResponse
    {
        $message = $this->service->fragile($product_parser);
        return redirect()->back()->with('success', $message);
    }

    public function sanctioned(ParserProduct $product_parser): \Illuminate\Http\RedirectResponse
    {
        $message = $this->service->sanctioned($product_parser);
        return redirect()->back()->with('success', $message);
    }

    public function parser(ParserProduct $product_parser): \Illuminate\Http\RedirectResponse
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
