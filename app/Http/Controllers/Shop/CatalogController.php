<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Repository\AttributeRepository;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogController extends Controller
{
    private CategoryRepository $repository;
    private ProductRepository $products;
    private AttributeRepository $attributes;

    public function __construct(CategoryRepository $repository, ProductRepository $products, AttributeRepository $attributes)
    {
        $this->repository = $repository;
        $this->products = $products;
        $this->attributes = $attributes;
    }

    public function view(Request $request, $slug)
    {
        try {
            $category = $this->repository->getBySlug($slug);

            if (count($category->children) > 0) return view('shop.catalogs', compact('category'));
            //$parents_id = $this->repository->relationAttributes()
            //$products
            $products = $this->products->getByCategory($category->id);

            $product_ids = [];
            foreach ($products as $product) {
                $product_ids[] = $product->id;
            }
            $_attr_cat = $this->attributes->getIdPossibleForCategory($category->getParentIdAll());
            $_attr_prod = $this->attributes->getIdPossibleForProducts($product_ids);
            $_attr_intersect =array_intersect($_attr_cat, $_attr_prod);
            $prod_attributes = Attribute::whereIn('id', $_attr_intersect)->orderBy('group_id')->get();
            //$prod_attributes = [];// $this->attributes->getPossibleForCategory($category->getParentIdAll());
//getIdPossibleForProducts
            //$products = $this->products->getFilter($category->id, $request);
            $tags = [];

            return view('shop.products', compact('category', 'products','prod_attributes', 'tags', '_attr_cat', '_attr_prod'));

        } catch (\Throwable $e) {
            $category = null;
            flash($e->getMessage(), 'danger');
            return redirect()->route('home'); //'error.403', ['message' => $e->getMessage()]
        }
    }

    public function search(Request $request)
    {
        $result = [];
        if (empty($request['category'])) return ;
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
