<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{

    private ShopRepository $repository;

    public function __construct(ShopRepository $repository)
    {
        $this->middleware(['auth:admin'])->only(['view_draft']);
        $this->repository = $repository;

    }

    public function view($slug)
    {
        $product = $this->repository->getProductBySlug($slug);
        if (empty($product) || !$product->isPublished()) {
            abort(404);
        }

        return $this->try_catch(function () use ($product) {
            $title = $product->name . ' купить по цене ' . $product->getLastPrice() . '₽ ☛ Доставка по всей России ★★★ Интернет-магазин Норди Хоум Калининград';
            $description = $product->short;
            $productAttributes = $this->repository->getProdAttributes($product);
            /*$productAttributes = [];
            foreach ($product->prod_attributes as $attribute) {
                $value = $attribute->Value();
                if (is_array($value)) {
                    $value = implode(', ', array_map(function ($id) use ($attribute) {
                        return $attribute->getVariant((int)$id)->name;
                    }, $attribute->Value()));
                }
                $productAttributes[$attribute->group->name][] = [
                    'name' => $attribute->name,
                    'value' => $value,
                ];
            }*/
            return view('shop.product.view', compact('product', 'title', 'description', 'productAttributes'));
        });
    }

    public function view_draft(Product $product)
    {
        if ($product->isPublished()) {
            flash('Товар опубликован, неверная ссылка');
            return redirect()->back();
        }

        return $this->try_catch(function () use ($product) {
            $title = 'Черновик ' . $product->name . ' купить по цене ' . $product->getLastPrice() . '₽ ☛ Доставка по всей России ★★★ Интернет-магазин Норди Хоум Калининград';
            $description = $product->short;
            $productAttributes = $this->repository->getProdAttributes($product);

            return view('shop.product.view', compact('product', 'title', 'description', 'productAttributes'));
        });
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

        return $this->try_catch_ajax(function () use ($request) {
            $result = $this->repository->search($request['search']);
            return \response()->json($result);
        });
    }


    public function count_for_sell(Product $product)
    {
        return response()->json($product->getCountSell());
    }
}
