<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Shop\Repository\CacheRepository;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;
use Illuminate\Http\Request;

class ProductController extends ShopController
{

    private ShopRepository $repository;
    private SlugRepository $slugs;
    private CacheRepository $caches;


    public function __construct(
        ShopRepository $repository,
        SlugRepository $slugs,
        CacheRepository $caches
    )
    {
        $this->middleware(['auth:admin'])->only(['view_draft']);
        parent::__construct();
        $this->repository = $repository;
        $this->slugs = $slugs;
        $this->caches = $caches;
    }

    public function view($slug)
    {
        $product = $this->slugs->getProductBySlug($slug);
        if (empty($product) || !$product->isPublished()) abort(404);
        $title = $product->name . ' купить по цене ' . $product->getPriceRetail() . '₽ ☛ Доставка по всей России ★★★ Интернет-магазин Норди Хоум Калининград';
        $description = $product->short;
        $productAttributes = $this->repository->getProdAttributes($product);
        //dd($productAttributes);
        $product = $this->caches->product_view_cache($product);

        return view($this->route('product.view'), compact('product', 'title', 'description', 'productAttributes'));
    }

    public function view_draft(Product $product)
    {
        if ($product->isPublished()) {
            flash('Товар опубликован, неверная ссылка');
            return redirect()->back();
        }
        $title = 'Черновик ' . $product->name . ' купить по цене ' . $product->getPriceRetail() . '₽ ☛ Доставка по всей России ★★★
        Интернет-магазин ' . $this->web->title_city;
        $description = $product->short;
        $productAttributes = $this->repository->getProdAttributes($product);
        return view($this->route('product.view'), compact('product', 'title', 'description', 'productAttributes'));
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
