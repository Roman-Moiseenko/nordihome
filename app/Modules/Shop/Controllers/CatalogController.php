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
use Illuminate\Support\Facades\Cache;

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
        $callback = function () use ($request, $slug) {

        };
        $page = $request->has('page');
        if (empty($request->all() || (count($request->all()) == 1 && $page)) && $this->web->is_cache) {
            //Без фильтра берем кэш
            set_time_limit(100);
            $cache_name = 'category-' . $slug . '-' . $request->string('page')->value();
            return Cache::rememberForever($cache_name, function () use ($request, $slug) {
                return $this->callback_view($request, $slug);
            });
        } else {

            return $this->callback_view($request, $slug);
        }

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

    /**
     * Генерация страницы категории
     */
    public function callback_view(Request $request, $slug)
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
        $query = $this->repository->filter($request, $product_ids);
        $count_in_category = $query->count();
        $products = $query->paginate($this->web->paginate);
        if (empty($category->title)) {
            $title = $category->name . ' купить по цене от ' . $minPrice . '₽ ☛ Низкие цены ☛ Большой выбор ☛ Доставка по всей России ★★★ Интернет-магазин ' .
                $this->web->title_city . ' ☎ ' . $this->web->title_contact;
        } else {
            $title = $category->title;
        }

        $products = $products->withQueryString()
            ->through(fn(Product $product) => $this->repository->ProductToArrayCard($product));


        return view($this->route('product.index'),
            compact('category', 'products', 'prod_attributes', 'tags',
                'minPrice', 'maxPrice', 'brands', 'request', 'title', 'description', 'tag_id', 'order', 'children', 'count_in_category'))->render();
    }
}
