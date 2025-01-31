<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Setting\Entity\Web;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends ShopController
{

    private ShopRepository $repository;


    public function __construct(ShopRepository $repository, SettingRepository $settings)
    {
        $this->middleware(['auth:admin'])->only(['view_draft']);
        parent::__construct();
        $this->repository = $repository;
        $this->web = $settings->getWeb();
    }

    public function view($slug)
    {
        $product = $this->repository->getProductBySlug($slug);
        if (empty($product) || !$product->isPublished()) abort(404);
        $title = $product->name . ' купить по цене ' . $product->getPriceRetail() . '₽ ☛ Доставка по всей России ★★★ Интернет-магазин Норди Хоум Калининград';
        $description = $product->short;
        $productAttributes = $this->repository->getProdAttributes($product);

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
