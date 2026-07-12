<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Application\Queries\Product\ProductViewQuery;
use App\Modules\Shop\Controllers\ShopController;
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
    )
    {
        $this->middleware(['auth:admin'])->only(['view_draft']);
        parent::__construct();
        $this->repository = $repository;
        $this->views = $views;
    }

    public function view(Request $request, $slug)
    {
        $data = $this->productViewQuery->execute($slug, $this->client);

        return view('shop.product.view', [
            'pageData' => $data,
        ]);
        //return $this->views->product($slug);
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

    //Ajax
    public function search(Request $request)
    {
        if (empty($request['search'])) return \response()->json(false);
        $result = $this->repository->search($request['search']);
        return \response()->json($result);
    }

    //TODO Переименовать
    public function count_for_sell(Product $product)
    {
        return response()->json($product->getQuantitySell());
    }
}
