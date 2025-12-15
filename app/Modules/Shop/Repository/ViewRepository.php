<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

use App\Modules\Base\Helpers\CacheHelper;
use App\Modules\Page\Entity\News;
use App\Modules\Page\Entity\Post;
use App\Modules\Page\Entity\PostCategory;
use App\Modules\Page\Entity\Widgets\Template;
use App\Modules\Page\Repository\MetaTemplateRepository;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use App\Modules\Shop\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ViewRepository
{
    private ShopRepository $repository;
    private SlugRepository $slugs;
    protected string $theme;
    public Web $web;
    private Schema $schema;
    private MetaTemplateRepository $seo;


    public function __construct(ShopRepository $repository,
                                SlugRepository $slugs, Schema $schema, MetaTemplateRepository $seo)
    {
        $this->repository = $repository;
        $this->slugs = $slugs;
        $settings = app()->make(Settings::class);
        $this->web = $settings->web;
        $this->theme = config('shop.theme');

        $this->schema = $schema;
        $this->seo = $seo;
    }

    public function product(string $slug): View
    {
        //$url_page = route('shop.product.view', $slug);

        $product = $this->slugs->getProductBySlug($slug);
        if (empty($product) || !$product->isPublished()) abort(404);

        $meta = $this->seo->seo($product);
        $title = $meta->title;
        $description = $meta->description;

        $productAttributes = $this->repository->getProdAttributes($product);
        $product = $this->product_view_cache($product);
        $schema = $this->schema_category_product($product);
        return view($this->route('product.view'),
            compact('product', 'title', 'description', 'productAttributes', 'schema'/*, 'url_page'*/));
    }

    public function root(array $request)
    {
        $url_page = route('shop.category.index');
        $title = $this->web->categories_title;
        $description = $this->web->categories_desc;
        $page = $request['page'] ?? 1;
        $schema = '';
        if ($this->web->is_category) {
            $categories = $this->repository->getChildren();
            return view($this->route('catalog'), compact('title', 'description', 'categories', 'url_page'));
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
        $in_stock = isset($request['in_stock']);

        $products = $this->category_products_cache(null, $in_stock);

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
        $prod_attributes = $this->root_attributes_cache($product_ids);

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
                compact('products', 'prod_attributes', 'tags',
                    'minPrice', 'maxPrice', 'brands', 'request', 'title', 'description', 'tag_id',
                    'order', 'children', 'count_in_category', 'schema', 'url_page', 'page'));
    }

    public function category(array $request, string $slug)
    {
        // $begin = now();
       // $url_page = route('shop.category.view', $slug);
        $category = $this->slugs->CategoryBySlug($slug);
        $page = $request['page'] ?? 1;
        if (is_null($category)) return abort(404);

        $meta = $this->seo->seo($category);
        $title = $meta->title;
        $description = $meta->description;

        //TODO Schema для парсер категории
        //$schema = $this->schema_category_cache($category);

        if ($this->web->is_category && count($category->children) > 0) {
            $children = $this->repository->getChildren($category->id);
            return view($this->route('subcatalog'), compact('category', 'children', 'title', 'description'));
        }

        $minPrice = 10;
        $maxPrice = 999999999;
        $brands = [];
        $children = $this->category_children_cache($category);
        $in_stock = isset($request['in_stock']);
        $products = $this->category_products_cache($category, $in_stock);

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
       // $title = $category->title;


        $products = $products->withQueryString()
            ->through(fn(Product $product) => $this->product_card_cache($product));


        //  $end = now();
        //     \Log::info('Для категории ' . $category->name . ' обсчет =  ' . $begin->diffInMilliseconds($end) / 1000);
        return view(
            $this->route('product.index'),
                compact('category', 'products', 'prod_attributes', 'tags',
                    'minPrice', 'maxPrice', 'brands', 'request', 'title', 'description', 'tag_id',
                    'order', 'children', 'count_in_category'/*, 'schema'*/, 'page'));
    }

    /**
     * @throws \Throwable
     */
    public function page($slug): string
    {
        $page = $this->slugs->PageBySlug($slug);

        if (is_null($page)) abort(404, 'Страница не найдена');
        return $page->view($this->seo->seoFn());
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

    private function category_products_cache(?Category $category, $in_stock)
    {
        $slug = is_null($category) ? 'root' : $category->slug;
        $callback = function () use ($category, $in_stock) {
            $products = $this->repository->ProductsByCategory($category); //0.07сек
            //Убираем из коллекции товары, которые не продаем под заказ
            return $products->reject(function (Product $product) use ($in_stock) {
                //  if ($product->getQuantitySell() == 0) dd($product->name);

                //if (!($product->getQuantitySell() > 0 || (!$in_stock && $product->pre_order))) {dd(json_encode([$product->name . " " . $product->getQuantitySell() . " " . !$in_stock . ' ' . $product->pre_order]));}
                return !($product->getQuantitySell() > 0 || (!$in_stock && $product->pre_order));
            });
        };

        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::CATEGORY_PRODUCTS . $slug, $callback);
        } else {
            return $callback();
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
            if (is_null($_category)) return [];
            if ($_category->children()->count() == 0) return [];
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
            return Cache::rememberForever(CacheHelper::CATEGORY_CHILDREN . $category->slug, $callback);
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

    private function root_attributes_cache(array $product_ids)
    {
        $callback = function () use ($product_ids) {
            $prod_attributes = [];

            foreach (Category::where('parent_id', null)->get() as $category) {
                $_arr = $this->repository->AttributeCommon(
                    array_merge($category->getParentIdAll(), $category->getChildrenIdAll()),
                    $product_ids);
                foreach ($_arr as $item) {
                    if ($item['isVariant']) {
                        $prod_attributes[$item['id']] = [
                            'id' => $item['id'],
                            'name' => $item['name'],
                            'isVariant' => $item['isVariant'],
                        ];

                        foreach ($item['variants'] as $variant) {
                            $prod_attributes[$item['id']]['variants'][$variant['id']] = $variant;
                        }

                    } else {
                        $prod_attributes[$item['id']] = $item;
                    }
                }
            }
            return $prod_attributes;
        };


        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::CATEGORY_ATTRIBUTES . 'root', $callback);
        } else {
            $callback();
        }
    }

    public function news(Request $request)
    {

        $news = News::orderByDesc('created_at')->active()->paginate($request->input('size', 20))->get();
        return view(
            Template::blade('page') . 'news',
            ['news' => $news,])
            ->render();
    }

    /**
     * @throws \Throwable
     */
    public function posts($slug): string
    {
        /** @var PostCategory $posts */
        $posts = $this->slugs->PostCategoryBySlug($slug);
        if (is_null($posts)) abort(404, 'Страница не найдена');
        return $posts->view($this->seo->seoFn());
    }

    /**
     * @throws \Throwable
     */
    public function post($slug): string
    {
        /** @var Post $post */
        $post = $this->slugs->PostBySlug($slug);
        //dd($post);
        if (is_null($post)) abort(404, 'Страница не найдена');
        return $post->view($this->seo->seoFn());
    }

    /// =====PARSER===== ///
    public function rootParser()
    {
        $url_page = route('shop.parser.view');
        $title = $this->web->categories_title;
        $description = $this->web->categories_desc;
       // $page = $request['page'] ?? 1;
       // $schema = '';

        $categories = $this->repository->getChildrenParser();
        return view($this->route('parser.catalog'), compact('title', 'description', 'categories', 'url_page'));

    }

    public function categoryParser(Request $request, $slug)
    {
        $category = $this->slugs->CategoryParserBySlug($slug);
        $page = $request['page'] ?? 1;
        if (is_null($category)) return abort(404);

        $meta = $this->seo->seo($category);
        //dd($meta);
        $title = $meta->title;
        $description = $meta->description;

        //$schema = $this->schema_category_cache($category);

        if ($this->web->is_category && $category->children()->count() > 0) {
            $children = $this->repository->getChildrenParser($category->id);

            return view($this->route('parser.subcatalog'), compact('category', 'children', 'title', 'description'));
        }



        $children = $this->parser_category_children_cache($category);

        //$in_stock = isset($request['in_stock']);
        $query = $this->repository->ParserProductsByCategory($category);//$this->parser_category_products_cache($category);


        $count_in_category = $query->count();
        $products = $query->paginate($this->web->paginate);
        //$title = $category->title;


        $products = $products->withQueryString()
            ->through(fn(ProductParser $product) => $this->parser_product_card_cache($product));


        //  $end = now();
        //     \Log::info('Для категории ' . $category->name . ' обсчет =  ' . $begin->diffInMilliseconds($end) / 1000);
        return view(
            $this->route('parser.product.index'),
            compact('category', 'products', 'request', 'title', 'description',
                'children', 'count_in_category',/* 'schema',*/ 'page'));

    }

    public function productParser($slug)
    {
        $product = $this->slugs->getProductParserBySlug($slug);
        if (is_null($product) || !$product->availability) abort(404);

        $meta = $this->seo->seo($product);
        $title = $meta->title;
        $description = $meta->description;

        //$productAttributes = $this->repository->getProdAttributes($product);
        $product = $this->parser_product_view_cache($product);
        //$schema = $this->schema_category_product($product);
        return view($this->route('parser.product.view'),
            compact('product', 'title', 'description'/*, 'schema'*/));
    }

    private function parser_product_view_cache(ProductParser $product)
    {
        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::PARSER_PRODUCT_CARD_VIEW . $product->id, function () use ($product) {
                return $this->repository->ParserProductToArrayView($product);
            });
        } else {
            return $this->repository->ParserProductToArrayView($product);
        }
    }
    private function parser_category_children_cache(CategoryParser $category)
    {
        $callback = function () use ($category) {
            if ($category->children()->count() == 0) {
                $_category = $category->parent;
            } else {
                $_category = $category;
            }
            if (is_null($_category)) return [];
            if ($_category->children()->count() == 0) return [];
            $children = $_category->children()
                ->where('active', true)->defaultOrder()
                ->get()->map(function (CategoryParser $category) {
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
            return Cache::rememberForever(CacheHelper::PARSER_CATEGORY_CHILDREN . $category->slug, $callback);
        } else {
            return $callback();
        }
    }

    private function parser_category_products_cache(?CategoryParser $category)
    {
        $slug = is_null($category) ? 'root' : $category->slug;
        $callback = function () use ($category) {
            return $this->repository->ParserProductsByCategory($category);
        };

        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::PARSER_CATEGORY_PRODUCTS . $slug, $callback);
        } else {
            return $callback();
        }
    }

    private function parser_product_card_cache(ProductParser $product)
    {
        if ($this->web->is_cache) {
            return Cache::rememberForever(CacheHelper::PARSER_PRODUCT_CARD . $product->id, function () use ($product) {
                return $this->repository->ParserProductToArrayCard($product);
            });
        } else {
            return $this->repository->ParserProductToArrayCard($product);
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
