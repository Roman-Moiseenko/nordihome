<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Modules\Catalog\Infrastructure\Models\Product;
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
       // \Log::warning(json_encode($data));
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


        $result = $this->repository->search($request['search']);
       // \Log::warning(json_encode($data));
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
