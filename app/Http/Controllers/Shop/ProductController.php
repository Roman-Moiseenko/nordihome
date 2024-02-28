<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{

    private ShopRepository $repository;

    public function __construct(ShopRepository $repository)
    {
        $this->repository = $repository;
    }

    public function view($slug)
    {
        $product = $this->repository->getProductBySlug($slug);
        if (empty($product)) {
            abort(404);
        }

        try {
            $title = $product->name . ' купить по цене ' . $product->getLastPrice() . '₽ ☛ Доставка по всей России ★★★ Интернет-магазин Норди Хоум Калининград';
            $description = $product->short;
            $productAttributes = [];
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
            }


            return view('shop.product.view', compact('product', 'title', 'description', 'productAttributes'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'info');
            event(new ThrowableHasAppeared($e));
        }
        return redirect()->back();
    }

    public function old_slug($old_slug)
    {
        $product = Product::where('old_slug', $old_slug)->first();
        if (empty($product)) abort(404);
        return redirect()->route('shop.product.view', $product->slug);
    }

    public function search(Request $request)
    {
        if (empty($request['search'])) return ;

        try {
            $result = $this->repository->search($request['search']);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            $result = ['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]];
        }
        return \response()->json($result);
    }

    //Ajax
    public function count_for_sell(Product $product)
    {
        return response()->json($product->count_for_sell);
    }
}
