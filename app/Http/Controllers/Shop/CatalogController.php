<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\AttributeRepository;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogController extends Controller
{
    private ShopRepository $repository;
    //private ProductRepository $products;
    private AttributeRepository $attributes;

    public function __construct(ShopRepository $repository, AttributeRepository $attributes)
    {
        $this->repository = $repository;
        $this->attributes = $attributes;
    }

    public function view(Request $request, $slug)
    {
      /*  try {*/
            $category = $this->repository->CategoryBySlug($slug);

            if (count($category->children) > 0) return view('shop.catalogs', compact('category'));

            $products = $this->repository->ProductsByCategory($category->id);

            $minPrice = 10;
            $maxPrice = 999999;
            $product_ids = [];
            $brands = [];
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

            $_attr_cat = $this->attributes->getIdPossibleForCategory($category->getParentIdAll());
            $_attr_prod = $this->attributes->getIdPossibleForProducts($product_ids);
            $_attr_intersect =array_intersect($_attr_cat, $_attr_prod);
            //Перебрать в массив и отобрать для Numeric - min/max значения, для Variants - только используемые варианты
            $attributes = Attribute::whereIn('id', $_attr_intersect)->where('filter', '=', true)->orderBy('group_id')->get();
            $prod_attributes = [];
            /** @var Attribute $attribute */
            foreach ($attributes as $attribute) {

                if ($attribute->isNumeric()) $prod_attributes[] = $this->attributes->getNumericAttribute($attribute, $product_ids);
                if ($attribute->isVariant()) $prod_attributes[] = $this->attributes->getVariantAttribute($attribute, $product_ids);
                if (!$attribute->isNumeric() && !$attribute->isVariant()) {
                    if ($attribute->isBool()) {
                        $prod_attributes[] = [
                            'id' => $attribute->id,
                            'name' => $attribute->name,
                            'isBool' => true,
                        ];
                    } else {
                        $prod_attributes[] = [
                            'id' => $attribute->id,
                            'name' => $attribute->name,
                        ];
                    }
                }
            }
            //$prod_attributes = [];// $this->attributes->getPossibleForCategory($category->getParentIdAll());
//getIdPossibleForProducts
            //$products = $this->products->getFilter($category->id, $request);
            $tags = [];

            return view('shop.products',
                compact('category', 'products','prod_attributes', 'tags', 'minPrice', 'maxPrice', 'brands'));

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
