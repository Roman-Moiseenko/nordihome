<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

use App\Modules\Base\Helpers\CacheHelper;
use App\Modules\Page\Entity\Page;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use App\Modules\Shop\Schema;
use Auth;
use Illuminate\Support\Facades\Cache;

class ViewRepository
{
    private ShopRepository $repository;
    private SlugRepository $slugs;
    protected string $theme;
    public Web $web;


    public function __construct(ShopRepository $repository,
                                SlugRepository $slugs)
    {
        $this->repository = $repository;
        $this->slugs = $slugs;
        $settings = app()->make(Settings::class);
        $this->web = $settings->web;
        $this->theme = config('shop.theme'); // $options->shop->theme;

    }

    public function product(string $slug): string
    {
     //   $categories = $this->categories_cache();
    //    $trees = $this->trees_cache();

        $schema = new Schema();
        $url_page = route('shop.product.view', $slug);

        $product = $this->slugs->getProductBySlug($slug);
        if (empty($product) || !$product->isPublished()) abort(404);
        $name = is_null($product->modification()) ? $product->name : $product->modification->name;
        //TODO Перенести во view !!!
        $title = $name . ' купить по цене ' . $product->getPrice() . '₽ ☛ Доставка по всей России ★★★ Интернет-магазин ' . $this->web->title_city;
        $description = 'Оригинальный ' . $name . ' из Европы. Бесплатная доставка по всей России. Только брендовая одежда и обувь. ';

        $productAttributes = $this->repository->getProdAttributes($product);
        if ($this->web->is_cache) {
            $product = $this->product_view_cache($product);
        } else {
            $product = $this->repository->ProductToArrayView($product);
        }

        return view($this->route('product.view'),
            compact('product', 'title', 'description', 'productAttributes', 'schema', 'url_page'))->render();
    }


    public function category(array $request, string $slug)
    {
      //  $categories = $this->categories_cache();
      //  $trees = $this->trees_cache();
        $schema = new Schema();
        $url_page = route('shop.category.view', $slug);
        $category = $this->slugs->CategoryBySlug($slug);
        if (is_null($category)) return abort(404);
        $title = $category->title;
        $description = $category->description;
        if ($this->web->is_category && count($category->children) > 0) {
            $children = $this->repository->getChildren($category->id);
            return view($this->route('subcatalog'), compact('category', 'children', 'title', 'description', 'url_page'))->render();
        }

        $minPrice = 10;
        $maxPrice = 999999999;
        $brands = [];
        $children = $category->children()->defaultOrder()->get()->map(function (Category $category) {
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
            $_price_product = $product->getPrice();
            if ($i == 0) {
                $minPrice = $_price_product;
                $maxPrice = $_price_product;
            } else {
                if ($_price_product < $minPrice) $minPrice = $_price_product;
                if ($_price_product > $maxPrice) $maxPrice = $_price_product;
            }
            if (!isset($brands[$product->brand->id]))
                $brands[$product->brand->id] = [
                    'name' => $product->brand->name,
                    'image' => $product->brand->getImage(),
                ];
        }

        $product_ids = $products->pluck('id')->toArray();
        if ($this->web->is_cache) {
            $prod_attributes = $this->category_attributes_cache($category, $product_ids);
        } else {
            $prod_attributes = $this->repository->AttributeCommon($category->getParentIdAll(), $product_ids);
        }


        $tags = $this->repository->TagsByProducts($product_ids);  //0.0015 сек
        $tag_id = $request['tag_id'] ?? null;
        $order = $request['order'] ?? 'name';
        $query = $this->repository->filter($request, $product_ids); //0.0015 сек
        $count_in_category = $query->count();

        $products = $query->paginate($this->web->paginate);
        if (empty($category->title)) {
            //TODO Перенести во view !!!
            $title = $category->name . ' купить по цене от ' . $minPrice . '₽ ☛ Низкие цены ☛ Большой выбор ☛ Доставка по всей России ★★★ Интернет-магазин ' .
                $this->web->title_city . ' ☎ ' . $this->web->title_contact;
        } else {
            $title = $category->title;
        }

        $products = $products->withQueryString()
            ->through(function (Product $product) {
                if ($this->web->is_cache) {
                    return $this->product_card_cache($product);
                } else {
                    return $this->repository->ProductToArrayCard($product);
                }

            });
        return view($this->route('product.index'),
            compact('category', 'products', 'prod_attributes', 'tags',
                'minPrice', 'maxPrice', 'brands', 'request', 'title', 'description', 'tag_id',
                'order', 'children', 'count_in_category', 'schema', 'url_page'))->render();
    }

    public function page($slug)
    {
        $page = $this->slugs->PageBySlug($slug); // Page::where('slug', $slug)->where('published', true)->firstOrFail();
        if (is_null($page)) abort(404, 'Страница не найдена');
        return $page->view();
    }


    final public function route(string $blade): string
    {
        return 'shop.' . $this->theme . '.' . $blade;
    }


    private function product_card_cache(Product $product)
    {
        return Cache::rememberForever('product-card-' . $product->id, function () use ($product) {
            return $this->repository->ProductToArrayCard($product);
        });
    }

    private function product_view_cache(Product $product)
    {
        return Cache::rememberForever('product-view-' . $product->id, function () use ($product) {
            return $this->repository->ProductToArrayView($product);
        });
    }

    private function category_attributes_cache(Category $category, array $product_ids)
    {
        return Cache::rememberForever('category-attributes-' . $category->slug, function () use ($category, $product_ids) {
            return $this->repository->AttributeCommon($category->getParentIdAll(), $product_ids); //0.02 секунды
        });
    }

    private function categories_cache()
    {
        return Cache::rememberForever(CacheHelper::MENU_CATEGORIES, function () {
            return $this->repository->getChildren();
        });
    }

    private function trees_cache()
    {
        return Cache::rememberForever(CacheHelper::MENU_TREES, function () {
            return $this->repository->getTree();
        });
    }
}
