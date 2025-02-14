<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CacheRepository
{
    private ShopRepository $repository;
    private SlugRepository $slugs;
    private Settings $settings;
    private Web $web;
    protected string $theme;

    public function __construct(ShopRepository $repository,
                                SlugRepository $slugs,
                                Settings       $settings,
    )
    {
        $this->repository = $repository;
        $this->slugs = $slugs;
        $this->settings = $settings;
        $this->web = $settings->web;
        $this->theme = config('shop.theme');
    }

    /**
     * Генерация страницы категории
     * Для запуска - Находим кол-во товаров в категории, делим на страницы (wep->pages) и передаем по порядку $request['page]
     */
    public function category_cache(array $request, $slug)
    {
        $category = $this->slugs->CategoryBySlug($slug);
        if (is_null($category)) return abort(404);

        $title = $category->title;
        $description = $category->description;

        if ($this->web->is_category && count($category->children) > 0) {
            $children = $this->repository->getChildren($category->id);
            return view($this->route('subcatalog'), compact('category', 'children', 'title', 'description'))->render();
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

        $in_stock = isset($request['in_stock']);
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


    final public function route(string $blade): string
    {
        return 'shop.' . $this->theme . '.' . $blade;
    }
}
