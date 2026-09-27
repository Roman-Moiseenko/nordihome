<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Modules\Analytics\Application\Services\TrackSearchService;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Application\Queries\Product\ProductViewQuery;
use App\Modules\Shop\Application\Queries\Search\FullSearchQuery;
use App\Modules\Shop\Application\Queries\Search\ProductSearchQuery;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Http\Request;

class ProductController extends ShopAbstractController
{

    public function __construct(
        private readonly ProductViewQuery   $productViewQuery,
        private readonly ProductSearchQuery $productSearchQuery,
        private readonly FullSearchQuery    $fullSearchQuery,
        private readonly TrackSearchService $trackSearchService,
    )
    {
        $this->middleware(['role:admin|staff'])->only(['view_draft']);
    }

    public function view(Request $request, $slug)
    {
        $client = $this->getClient($request);
        $data = $this->productViewQuery->execute($slug, $client);

        return view('shop.product.view', [
            'pageData' => $data,
        ]);
    }

    public function searchIndex(Request $request)
    {
        $search = $request->string('search')->trim()->value();
        $client = $this->getClient($request);
        $data = $this->productSearchQuery->execute($search, $request->all(), $client);

        $this->trackSearchService->execute($search, $data->paginator->total);

        return view('shop.product.search', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);
    }

    //Ajax
    public function search(Request $request)
    {
        $search = $request->string('search')->trim()->value();
        if (empty($search)) return \response()->json(false);
        $client = $this->getClient($request);
        $data = $this->fullSearchQuery->execute($search, $client);

        //Учитываем только нулевой результат
        if (empty($data->products)) {
            $this->trackSearchService->execute($search, 0);
        }
        return \response()->json($data);
    }

    public function view_draft(Request $request, Product $product)
    {
        //FixMe переделать под id без Product
        $client = $this->getClient($request);
        $data = $this->productViewQuery->execute($product->slug, $client, false);

        return view('shop.product.view', [
            'pageData' => $data,
        ]);
    }


    //TODO Переименовать
    public function count_for_sell(Product $product)
    {
        return response()->json($product->getQuantitySell());
    }
}
