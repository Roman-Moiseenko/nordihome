<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Modules\Analytics\Application\Actions\Search\TrackSearchUseCase;
use App\Modules\Analytics\Domain\Interfaces\VisitorContextInterface;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Application\DTOs\Search\ProductSearchPageData;
use App\Modules\Shop\Application\Queries\Product\ProductViewQuery;
use App\Modules\Shop\Application\Queries\Search\FullSearchQuery;
use App\Modules\Shop\Application\Queries\Search\ProductSearchQuery;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Http\Request;

class ProductController extends ShopController
{
    private ShopRepository $repository;
    private ViewRepository $views;

    public function __construct(
        ShopRepository $repository,
        ViewRepository $views,
        private ProductViewQuery $productViewQuery,
        private ProductSearchQuery $productSearchQuery,
        private FullSearchQuery $fullSearchQuery,
        private TrackSearchUseCase $trackSearch,
        private VisitorContextInterface $analyticsContext,
    )
    {
        $this->middleware(['role:admin'])->only(['view_draft']);
      //  parent::__construct();
        $this->repository = $repository;
        $this->views = $views;
    }

    public function view(Request $request, $slug)
    {
        $client = $this->getClient($request);
        $data = $this->productViewQuery->execute($slug, $client);

        return view('shop.product.view', [
            'pageData' => $data,
        ]);
        //return $this->views->product($slug);
    }

    public function searchIndex(Request $request)
    {
        $search = $request->string('search')->trim()->value();
        $client = $this->getClient($request);
        $data = $this->productSearchQuery->execute($search, $request->all(), $client);

        $this->trackSearch($search, $data);

        return view('shop.product.search', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);

    }

    /**
     * Фиксирует строку поиска (переход на страницу результатов).
     * Идентификаторы берём из VisitorContext, заполненного middleware.
     */
    private function trackSearch(string $query, ?ProductSearchPageData $data): void
    {
        $visitorId = $this->analyticsContext->getVisitorId();
        if ($visitorId === null) {
            return;
        }

        $this->trackSearch->execute(
            $visitorId,
            $this->analyticsContext->getSessionId(),
            $this->analyticsContext->getPageViewId(),
            $query,
            $data?->paginator?->total ?? 0,
        );
    }
    //Ajax
    public function search(Request $request)
    {
        $search = $request->string('search')->trim()->value();
        if (empty($search)) return \response()->json(false);
        $client = $this->getClient($request);
        $data = $this->fullSearchQuery->execute($search, $client);
        return \response()->json($data);
    }

    public function view_draft(Product $product)
    {
        if ($product->isPublished()) {
            flash('Товар опубликован, неверная ссылка');
            return redirect()->back();
        }

        return $this->views->product_draft($product->slug);
  /*
        $title = 'Черновик ' . $product->name . ' купить по цене ' . $product->getPriceRetail() . '₽ ☛ Доставка по всей России ★★★
        Интернет-магазин ' . $this->web->title_city;
        $description = $product->short;
        $productAttributes = $this->repository->getProdAttributes($product);
        return view($this->route('product.view'), compact('product', 'title', 'description', 'productAttributes'));
*/
    }

    public function old_slug($old_slug)
    {
        $product = Product::where('old_slug', $old_slug)->first();
        if (empty($product)) abort(404);
        return redirect()->route('shop.product.view', $product->slug);
    }



    //TODO Переименовать
    public function count_for_sell(Product $product)
    {
        return response()->json($product->getQuantitySell());
    }
}
