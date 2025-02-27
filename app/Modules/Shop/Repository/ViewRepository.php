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
    private Schema $schema;


    public function __construct(ShopRepository $repository,
                                SlugRepository $slugs, Schema $schema)
    {
        $this->repository = $repository;
        $this->slugs = $slugs;
        $settings = app()->make(Settings::class);
        $this->web = $settings->web;
        $this->theme = config('shop.theme'); // $options->shop->theme;

        $this->schema = $schema;
    }

    public function product(string $slug)
    {
        $url_page = route('shop.product.view', $slug);

        $product = $this->slugs->getProductBySlug($slug);
        if (empty($product) || !$product->isPublished()) abort(404);
        $title = '';
        $description = '';

        $productAttributes = $this->repository->getProdAttributes($product);
        $product = $this->product_view_cache($product);
        $schema = $this->schema_category_product($product);
        return view($this->route('product.view'),
            compact('product', 'title', 'description', 'productAttributes', 'schema', 'url_page'));
    }

    public function root(array $request)
    {
        $url_page = route('shop.category.index');
        $title = $this->web->categories_title;
        $description = $this->web->categories_desc;
        $page = $request['page'] ?? 1;
        $schema = '';
        if ($this->web->is_category ) {
            $categories = $this->repository->getChildren();
            return view($this->route('catalog'), compact( 'title', 'description', 'categories', 'url_page'));
        }
        $minPrice = 10;
        $maxPrice = 999999999;
        $brands = [];
        $children = Category::defaultOrder()->where('parent_id', null)->get()->map(function (Category $category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ];
        });
        $products = $this->repository->ProductsByCategory();
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
        $_prod_attributes = [];
        foreach (Category::where('parent_id', null)->get() as $category) {
            $_prod_attributes = array_merge($_prod_attributes, $this->category_attributes_cache($category, $product_ids));
        }
        $prod_attributes = [];
        foreach ($_prod_attributes as $item) {
            $prod_attributes[$item['id']] = $item;
        }

        $tags = $this->repository->TagsByProducts($product_ids);  //0.0015 сек
        $tag_id = $request['tag_id'] ?? null;
        $order = $request['order'] ?? 'name';
        $query = $this->repository->filter($request, $product_ids); //0.0015 сек
        $count_in_category = $query->count();
        $products = $query->paginate($this->web->paginate);


        $products = $products->withQueryString()
            ->through(fn(Product $product) => $this->product_card_cache($product));
        return view(
            $this->route('product.index'),
            array_merge(
                compact( 'products', 'prod_attributes', 'tags',
                    'minPrice', 'maxPrice', 'brands', 'request', 'title', 'description', 'tag_id',
                    'order', 'children', 'count_in_category', 'schema', 'url_page', 'page'),
                [
                    'web' => $this->web,
                ]));
    }

    public function category(array $request, string $slug)
    {
        $url_page = route('shop.category.view', $slug);
        $category = $this->slugs->CategoryBySlug($slug);
        $page = $request['page'] ?? 1;
        if (is_null($category)) return abort(404);
        $title = $category->title;
        $description = $category->description;
        $schema = $this->schema_category_cache($category);
        if ($this->web->is_category && count($category->children) > 0) {
            $children = $this->repository->getChildren($category->id);
            return view($this->route('subcatalog'), compact('category', 'children', 'title', 'description', 'url_page'));
        }

        $minPrice = 10;
        $maxPrice = 999999999;
        $brands = [];
        $begin = now();
        $children = $this->category_children_cache($category);
        /*
        if ($category->children()->count() == 0) {
            $_category = $category->parent;
        } else {
            $_category = $category;
        }

        $children = $_category->children()->defaultOrder()->get()->map(function (Category $category) {
            if ($category->allProducts()->count() == 0) return null;
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ];
        });

        $children = array_filter($children->toArray());
        */
        $end = now();
        $products = $this->repository->ProductsByCategory($category);

        \Log::info('Для категории ' . $category->name . ' обсчет =  ' . $begin->diffInMilliseconds($end) / 1000);

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

        $prod_attributes = $this->category_attributes_cache($category, $product_ids);
        $tags = $this->repository->TagsByProducts($product_ids);  //0.0015 сек
        $tag_id = $request['tag_id'] ?? null;
        $order = $request['order'] ?? 'name';
        $query = $this->repository->filter($request, $product_ids); //0.0015 сек
        $count_in_category = $query->count();
        $products = $query->paginate($this->web->paginate);
        $title = $category->title;

        $products = $products->withQueryString()
            ->through(fn(Product $product) => $this->product_card_cache($product));
        return view(
            $this->route('product.index'),
            array_merge(
                compact('category', 'products', 'prod_attributes', 'tags',
                    'minPrice', 'maxPrice', 'brands', 'request', 'title', 'description', 'tag_id',
                    'order', 'children', 'count_in_category', 'schema', 'url_page', 'page'),
                [
                    'web' => $this->web,
                ]));
    }

    public function page($slug)
    {
        $page = $this->slugs->PageBySlug($slug);

        \Log::info('PageBySlug ' . $slug);
        // Page::where('slug', $slug)->where('published', true)->firstOrFail();
        if (is_null($page)) abort(404, 'Страница не найдена');
        return $page->view();
    }


    final public function route(string $blade): string
    {
        return 'shop.' . $this->theme . '.' . $blade;
    }


    private function product_card_cache(Product $product)
    {
        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::PRODUCT_CARD . $product->id, function () use ($product) {
                return $this->repository->ProductToArrayCard($product);
            });
        } else {
            return $this->repository->ProductToArrayCard($product);
        }
    }

    private function product_view_cache(Product $product)
    {
        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::PRODUCT_VIEW . $product->id, function () use ($product) {
                return $this->repository->ProductToArrayView($product);
            });
        } else {
            return $this->repository->ProductToArrayView($product);
        }
    }

    private function category_children_cache(Category $category)
    {
        $callback = function () use ($category) {
            if ($category->children()->count() == 0) {
                $_category = $category->parent;
            } else {
                $_category = $category;
            }

            $children = $_category->children()->defaultOrder()->get()->map(function (Category $category) {
                if ($category->allProducts()->count() == 0) return null;
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            });

            return array_filter($children->toArray());
        };
        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::CATEGORY_ATTRIBUTES . $category->slug, $callback);
        } else {
            return $callback();
        }
    }

    private function category_attributes_cache(Category $category, array $product_ids)
    {
        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::CATEGORY_ATTRIBUTES . $category->slug, function () use ($category, $product_ids) {
                return $this->repository->AttributeCommon(array_merge($category->getParentIdAll(), $category->getChildrenIdAll()), $product_ids); //0.02 секунды
            });
        } else {
            return $this->repository->AttributeCommon(array_merge($category->getParentIdAll(), $category->getChildrenIdAll()), $product_ids); //0.02 секунды
        }
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

    private function schema_category_cache(Category $category)
    {
        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::CATEGORY_SCHEMA . $category->slug, function () use ($category) {
                return $this->schema->CategoryProductsPage($category);
            });
        } else {
            return $this->schema->CategoryProductsPage($category);
        }
    }

    private function schema_category_product(mixed $product)
    {
        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::PRODUCT_SCHEMA . $product['slug'], function () use ($product) {
                return $this->schema->ProductPage($product);
            });
        } else {
            return $this->schema->ProductPage($product);
        }
    }


}
