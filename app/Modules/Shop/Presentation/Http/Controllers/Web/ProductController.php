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
    //  private ShopRepository $repository;
    private ViewRepository $views;

    public function __construct(
        ViewRepository             $views,
        private ProductViewQuery   $productViewQuery,
        private ProductSearchQuery $productSearchQuery,
        private FullSearchQuery    $fullSearchQuery,
        private TrackSearchService $trackSearchService,
    )
    {
        $this->middleware(['role:admin'])->only(['view_draft']);
        $this->views = $views;
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

    public function view_draft(Product $product)
    {
        if ($product->isPublished()) {
            flash('Товар опубликован, неверная ссылка');
            return redirect()->back();
        }
//TODO Переделать под UseCase

        return $this->views->product_draft($product->slug);
    }



    //TODO Переименовать
    public function count_for_sell(Product $product)
    {
        return response()->json($product->getQuantitySell());
    }
}
