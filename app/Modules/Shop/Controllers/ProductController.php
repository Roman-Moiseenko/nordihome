<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Shop\Repository\CacheRepository;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Http\Request;

class ProductController extends ShopController
{
    private ShopRepository $repository;
    private CacheRepository $caches;
    private ViewRepository $views;

    public function __construct(
        ShopRepository $repository,
        CacheRepository $caches,
        ViewRepository $views,
    )
    {
        $this->middleware(['auth:admin'])->only(['view_draft']);
        parent::__construct();
        $this->repository = $repository;
        $this->caches = $caches;
        $this->views = $views;
    }

    public function view($slug)
    {
        if ($this->web->is_cache) {
            return $this->caches->product($slug);
        } else {
            return $this->views->product($slug);
        }
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
