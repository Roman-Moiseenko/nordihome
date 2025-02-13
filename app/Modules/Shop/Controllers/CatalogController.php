<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends ShopController
{
    private ShopRepository $repository;
    private SlugRepository $slugs;

    public function __construct(
        ShopRepository $repository,
        SlugRepository $slugs,
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->slugs = $slugs;
    }

    public function index()
    {
       // $categories = $this->repository->getRootCategories();

        $title = $this->web->categories_title;
        $description = $this->web->categories_desc;

        return view($this->route('catalog'), compact( 'title', 'description'));
    }

    public function view(Request $request, $slug)
    {
        $category = $this->slugs->CategoryBySlug($slug);
        if (is_null($category)) return abort(404);


        $title = $category->title;
        $description = $category->description;

        if ($this->web->is_category && count($category->children) > 0) {
            $children = $this->repository->getChildren($category->id);
            return view($this->route('subcatalog'), compact('category', 'children', 'title', 'description'));
        }

        //TODO Переделать в запросы 1. получить только id Product,
        // 2. Получить мин и макс цены из таблицы напрямую whereIn($product_id, $product_ids), 3. Также получить бренды

        $minPrice = 10;
        $maxPrice = 999999999;

        $brands = [];
        $children = $category->children()->get()->map(function (Category $category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                ];
        });
        $products = $this->repository->ProductsByCategory($category);

        $in_stock = $request->has('in_stock');
        //Убираем из коллекции товары, которые не продаем под заказ
        $products = $products->reject(function (Product $product) use ($in_stock) {
            return !($product->getQuantitySell() > 0 || (!$in_stock && $product->pre_order));
        });

        /** @var Product $product */
        foreach ($products as $i => $product) {
            if ($i == 0) {
                $minPrice = $product->getPrice();
                $maxPrice = $product->getPrice();
            } else {
                if ($product->getPrice() < $minPrice) $minPrice = $product->getPrice();
                if ($product->getPrice() > $maxPrice) $maxPrice = $product->getPrice();
            }
            $brands[$product->brand->id] = [
                'name' => $product->brand->name,
                'image' => $product->brand->getImage(),
            ];
        }

        $product_ids = $products->pluck('id')->toArray();


        $prod_attributes = $this->repository->AttributeCommon($category->getParentIdAll(), $product_ids);

        $tags = $this->repository->TagsByProducts($product_ids);
        $tag_id = $request['tag_id'] ?? null;
        $order = $request['order'] ?? 'name';
        $products = $this->repository->filter($request, $product_ids);
        if (empty($category->title)) {
            $title = $category->name . ' купить по цене от ' . $minPrice . '₽ ☛ Низкие цены ☛ Большой выбор ☛ Доставка по всей России ★★★ Интернет-магазин ' .
                $this->web->title_city . ' ☎ ' . $this->web->title_contact;
        } else {
            $title = $category->title;
        }
            /*'NORDI HOME ' .
            ' Калининград ☎ [+7(4012) 37-37-30] (Круглосуточно)';*/
        //Переводим коллекцию в массив

        $products = $products->withQueryString()
            ->through(fn(Product $product) => $this->repository->ProductToArrayCard($product));


        return view($this->route('product.index'),
            compact('category', 'products', 'prod_attributes', 'tags',
                'minPrice', 'maxPrice', 'brands', 'request', 'title', 'description', 'tag_id', 'order', 'children'));

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
