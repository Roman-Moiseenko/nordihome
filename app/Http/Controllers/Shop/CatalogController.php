<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\AttributeRepository;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogController extends Controller
{
    private ShopRepository $repository;

    //private AttributeRepository $attributes;

    public function __construct(ShopRepository $repository/*, AttributeRepository $attributes*/)
    {
        $this->repository = $repository;
        //$this->attributes = $attributes;
    }

    public function view(Request $request, $slug)
    {
        /*  try {*/
        $category = $this->repository->CategoryBySlug($slug);
        if (count($category->children) > 0) return view('shop.catalogs', compact('category'));

        //TODO Переделать в запросы 1. получить только id Product,
        // 2. Получить мин и макс цены из таблицы напрямую whereIn($product_id, $product_ids), 3. Также получить бренды
        $minPrice = 10;
        $maxPrice = 9999999;
        $product_ids = [];
        $brands = [];
        $products = $this->repository->ProductsByCategory($category->id);
        /** @var Product $product */
        foreach ($products as $i => $product) {
            if ($i == 0) {
                $minPrice = $product->getLastPrice();
                $maxPrice = $product->getLastPrice();
            } else {
                if ($product->getLastPrice() < $minPrice) $minPrice = $product->getLastPrice();
                if ($product->getLastPrice() > $maxPrice) $maxPrice = $product->getLastPrice();
            }
            $product_ids[] = $product->id;
            $brands[$product->brand->id] = [
                'name' => $product->brand->name,
                'image' => $product->brand->getImage(),
            ];
        }

        $prod_attributes = $this->repository->AttributeCommon($category->getParentIdAll(), $product_ids);

        $tags = $this->repository->TagsByProducts($product_ids);
        $products = $this->repository->filter($request, $product_ids);

        return view('shop.products',
            compact('category', 'products', 'prod_attributes', 'tags', 'minPrice', 'maxPrice', 'brands', 'request'));

        /*  } catch (\Throwable $e) {
              $category = null;
              flash($e->getMessage(), 'danger');
              return redirect()->route('home'); //'error.403', ['message' => $e->getMessage()]
          }*/
    }

    public function search(Request $request)
    {
        $result = [];
        if (empty($request['category'])) return \response()->json($result);
        try {
            $categories = $this->repository->getTree((int)$request['category']);
            /** @var Category $category */
            foreach ($categories as $category) {
                $result[] = $this->repository->toShopForSubMenu($category);
            }
        } catch (\Throwable $e) {
            $result = $e->getMessage();
        }
        return \response()->json($result);
    }
}
