<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\AttributeRepository;
use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Web;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class CatalogController extends Controller
{
    private ShopRepository $repository;

    private Common $common;
    private Web $web;

    public function __construct(ShopRepository $repository, SettingRepository $settings)
    {
        $this->repository = $repository;
        $this->web = $settings->getWeb();
        $this->common = $settings->getCommon();
    }

    public function index()
    {
        $categories = $this->repository->getRootCategories();

        $title = $this->web->categories_title;
        $description = $this->web->categories_desc;

        return view('shop.catalog', compact('categories', 'title', 'description'));
    }

    public function view(Request $request, $slug)
    {
        $category = $this->repository->CategoryBySlug($slug);
        if (is_null($category)) return abort(404);


        $title = $category->title;
        $description = $category->description;

        if (count($category->children) > 0) {
            $children = $this->repository->getChildren($category->id);
            return view('shop.subcatalog', compact('category', 'children', 'title', 'description'));
        }

        //TODO Переделать в запросы 1. получить только id Product,
        // 2. Получить мин и макс цены из таблицы напрямую whereIn($product_id, $product_ids), 3. Также получить бренды

        $minPrice = 10;
        $maxPrice = 999999999;
        $product_ids = [];
        $brands = [];

        $products = $this->repository->ProductsByCategory($category->id);

        /** @var Product $product */
        foreach ($products as $i => $product) {
            if ($i == 0) {
                $minPrice = $product->getPrice();
                $maxPrice = $product->getPrice();
            } else {
                if ($product->getPrice() < $minPrice) $minPrice = $product->getPrice();
                if ($product->getPrice() > $maxPrice) $maxPrice = $product->getPrice();
            }

            if ($request->has('in_stock')) {
                if ($product->getQuantitySell() > 0)
                    $product_ids[] = $product->id;
            } else {
                if ($this->common->pre_order || $product->pre_order || $product->getQuantitySell() > 0)
                    $product_ids[] = $product->id;
            }

            $brands[$product->brand->id] = [
                'name' => $product->brand->name,
                'image' => $product->brand->getImage(),
            ];
        }

        $prod_attributes = $this->repository->AttributeCommon($category->getParentIdAll(), $product_ids);

        $tags = $this->repository->TagsByProducts($product_ids);
        $tag_id = $request['tag_id'] ?? null;
        $order = $request['order'] ?? 'name';
        $products = $this->repository->filter($request, $product_ids);

        $title = $category->name . ' купить по цене от ' . $minPrice . '₽ ☛ Низкие цены ☛ Большой выбор ☛ Доставка по всей России ★★★ Интернет-магазин ' .
            $this->web->title_city . ' ☎ ' . $this->web->title_contact;
            /*'NORDI HOME ' .
            ' Калининград ☎ [+7(4012) 37-37-30] (Круглосуточно)';*/

        return view('shop.product.index',
            compact('category', 'products', 'prod_attributes', 'tags',
                'minPrice', 'maxPrice', 'brands', 'request', 'title', 'description', 'tag_id', 'order'));

    }


    public function search(Request $request): JsonResponse
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
            event(new ThrowableHasAppeared($e));
            $result = ['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]];
        }
        return \response()->json($result);
    }
}
