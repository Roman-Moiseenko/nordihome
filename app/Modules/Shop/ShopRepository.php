<?php
declare(strict_types=1);

namespace App\Modules\Shop;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\Page;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeProduct;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Tag;
use App\Modules\Setting\Entity\Web;
use App\Modules\Setting\Repository\SettingRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopRepository
{

    private Web $web;

    public function __construct()
    {
        $settingRepository = new SettingRepository();
        $this->web = $settingRepository->getWeb();
    }

    public function getProductBySlug($slug):? Product
    {
        if (is_numeric($slug)) return Product::findOrFail($slug);
        return Product::where('slug', '=', $slug)->first();
    }

    public function maxPrice(array $product_ids)
    {
        //ProductPricing::whereIn('product_id', $product_ids)->where
    }

    public function search(string $search, int $take_cat = 3, int $take_prod = 7): array
    {
        $result = [];
        $search_back = $this->avto_replace($search);

        //Ищем Категории
        $categories = Category::orderBy('name')->where(function ($query) use ($search, $search_back) {
            $query->where('name', 'LIKE', "% {$search}%")->orWhere('name', 'LIKE', "{$search}%")
                ->orWhere('name', 'LIKE', "% {$search_back}%")->orWhere('name', 'LIKE', "{$search_back}%");
        })->take($take_cat)->get();

        foreach ($categories as $category) {
            $result[] = $this->CategoriesForSearch($category);
        }

        //Ищем Продукты
        $products = Product::orderBy('name')->where(function ($query) use ($search, $search_back) {
            $query->where('code_search', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "% {$search}%")->orWhere('name', 'LIKE', "{$search}%")
                ->orWhere('name', 'LIKE', "% {$search_back}%")->orWhere('name', 'LIKE', "{$search_back}%");
        })->take($take_prod)->get();

        foreach ($products as $product) {
            $result[] = $this->ProductsForSearch($product);
        }
        return $result;
    }

    public function filter(Request $request, array $product_ids)
    {
        $query = Product::orderByDesc('priority');

        $query = match ($request['order']) {
            'price-down' => $query->orderBy('current_price', 'desc'),
            'price-up' => $query->orderBy('current_price', 'asc'),
            'rating' => $query->orderBy('current_rating', 'asc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('name', 'asc'),
        };

       // $query = Product::orderBy('name');

        ///Фильтрация по $request

        //Теги
        if (!empty($tag = $request['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('id', $tag);
            });
        }
        //Бренд
        if (!empty($brands = $request['brands'])) {
            $query->whereIn('brand_id', $brands);
        }

        //TODO Наличие
      /*  if (!empty($request['in_stock'])) {
            $query->where('count_for_sell', '>', 0);
        }*/
        //Цена
        if (isset($request['price'])) {
            if (!empty($min = $request['price'][0]) && is_numeric($min)) {
                $query->whereHas('priceRetail', function ($q) use ($min) {
                    $q->where('value', '>=', $min);
                });
            }
            if (!empty($max = $request['price'][1]) && is_numeric($max)) {
                $query->whereHas('priceRetail', function ($q) use ($max) {
                    $q->where('value', '<=', $max);
                });
            }
        }

        //Акция -?

        //Атрибуты
        foreach ($request->all() as $key => $item) {
            if (str_contains($key, 'a_')) {
                $attr_id = (int)substr($key, 2, strlen($key) - 2);
                /** @var Attribute $attr */

                $attr = Attribute::find($attr_id);
                if ($attr->isBool()) {
                    $query->whereHas('prod_attributes', function ($q) use ($attr_id) {
                        $q->where('attribute_id', '=', $attr_id);
                    });
                }
                if ($attr->isNumeric()) {
                    $min = $item[0];
                    $max = $item[1];
                    //Получаем все позиции, где товары для текущего атрибута
                    $_attr_prods = AttributeProduct::where('attribute_id', '=', $attr_id)->whereIn('product_id', $product_ids)->get();

                    //Исключаем id товара, которые не удовлетворяют условиям > или <
                    foreach ($_attr_prods as $_attr_prod) {
                        $_value = (int)json_decode($_attr_prod->value);

                        if ((is_numeric($min) && $_value < (int)$min) || (is_numeric($max) && $_value > (int)$max))
                            $product_ids = array_filter($product_ids, function ($value) use ($_attr_prod) {
                                return $value != $_attr_prod->product_id;
                            });
                    }
                    $_temp_array = [];
                    foreach ($product_ids as $product_id) { //Формируем одномерный, не ассоциативный массив
                        $_temp_array[] = $product_id;
                    }
                    $product_ids = $_temp_array;
                }

                if ($attr->isVariant()) {
                    $query->whereHas('prod_attributes', function ($q) use ($item) {
                        foreach ($item as $k => $_od) {
                            if ($k == 0) {
                                $q->whereJsonContains('value', $_od);
                            } else {
                                $q->orWhereJsonContains('value', $_od);
                            }
                        }
                    });
                }

            }
        }

        return $query->whereIn('id', $product_ids)->paginate($this->web->paginate);
    }


    /*
        public function searchProduct(string $search, int $take = 10, array $include_ids = [], bool $isInclude = true)
        {
            $search_back = $this->avto_replace($search);

            $query = Product::orderBy('name')->where(function ($query) use ($search, $search_back) {
                $query->where('code_search', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "% {$search}%")->orWhere('name', 'LIKE', "{$search}%")
                    ->orWhere('name', 'LIKE', "% {$search_back}%")->orWhere('name', 'LIKE', "{$search_back}%");
            });

            if (!empty($include_ids)) {
                if ($isInclude) {
                    $query = $query->whereIn('id', $include_ids);
                } else {
                    $query = $query->whereNotIn('id', $include_ids);
                }
            }

            if (!is_null($take)) {
                $query = $query->take($take);
            } else {
                //$query = $query->all();
            }
            return $query->get();
        }
    */
    public function ProductsByCategory(int $id)
    {

        $query = Product::where('published', true);
        //TODO Показывать товары снятые с продажи?
        // $query->where('not_sale', false);

        $query->where(function ($_query) use ($id) {
            $_query->where('main_category_id', '=', $id)->OrWhere(function ($query) use ($id) {
                $query->whereHas('categories', function ($_query) use ($id) {
                    $_query->where('category_id', $id);
                });
            });
        });


        //Предзаказ
        //TODO Фильтр по наличию ????
    /*    if (!$this->options->shop->pre_order) {
            $query->where('count_for_sell', '>', 0);
        } else {
            $query->where(function ($_query) {
                $_query->where('pre_order', true)->OrWhere('count_for_sell', '>', 0);
            });
        }
*/
        return $query->get();
    }

    ////КАТЕГОРИИ
    ///
    public function CategoryBySlug($slug):? Category
    {
        if (is_numeric($slug)) {
            $category = Category::find($slug);
        } else {
            $category = Category::where('slug', '=', $slug)->first();
        }
        return $category;
    }

    /*
        public function searchCategory(string $search, int $take = 10)
        {
            $query = Category::orderBy('name')->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "% {$search}%")->orWhere('name', 'LIKE', "{$search}%");
            });
            return $query->take($take)->get();
        }
    */

    public function getTree(int $parent_id = null)
    {
        if (is_null($parent_id)) return Category::defaultOrder()->get()->toTree();
        return Category::defaultOrder()->descendantsOf($parent_id)->toTree();
    }

    public function toShopForSubMenu(Category $category): array
    {
        $children = [];
        if (!empty($category->children)) {
            foreach ($category->children as $child) {
                $children[] = $this->toShopForSubMenu($child);
            }
        }

        return [
            'id' => $category->id,
            'name' => $category->name,
            'url' => route('shop.category.view', $category->slug),
            'image' => !is_null($category->image) ? $category->image->getUploadUrl() : '',
            'products' => count($category->products),
            'children' => $children,
        ];
    }


    ////АТРИБУТЫ
    ///
    public function AttributeCommon(array $categories_id, array $product_ids)
    {
        $attrs_cat = Attribute::whereHas('categories', function ($query) use ($categories_id) {
            $query->whereIn('category_id', $categories_id);
        })->pluck('id')->toArray();

        $attrs_prod = Attribute::whereHas('products', function ($query) use ($product_ids) {
            $query->whereIn('product_id', $product_ids);
        })->pluck('id')->toArray();

        $_attr_intersect = array_intersect($attrs_cat, $attrs_prod); //Общие id атрибутов для товаров и категорий

        $attributes = Attribute::whereIn('id', $_attr_intersect)->where('filter', '=', true)->orderBy('group_id')->get();
        $prod_attributes = [];

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {  //Заполняем варианты и мин.и макс. значения из возможных для данных товаров
            if ($attribute->isNumeric()) $prod_attributes[] = $this->getNumericAttribute($attribute, $product_ids);
            if ($attribute->isVariant()) $prod_attributes[] = $this->getVariantAttribute($attribute, $product_ids);
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
        return $prod_attributes;
    }

    private function getNumericAttribute(Attribute $attribute, array $product_ids): array
    {
        $attr = array_map(function ($item) {
            return json_decode($item);
        }, AttributeProduct::where('attribute_id', '=', $attribute->id)->whereIn('product_id', $product_ids)->pluck('value')->toArray());

        return [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'isNumeric' => true,
            'min' => min($attr),
            'max' => max($attr)
        ];
    }

    private function getVariantAttribute(Attribute $attribute, array $product_ids)
    {
        $values = array_map(function ($item) {
            return json_decode($item);
        }, AttributeProduct::where('attribute_id', '=', $attribute->id)->whereIn('product_id', $product_ids)->pluck('value')->toArray());

        $variant_ids = [];
        foreach ($values as $item) {
            $variant_ids = array_merge($variant_ids, $item);
        }
        $variant_ids = array_unique($variant_ids);

        $variants = [];
        foreach ($variant_ids as $item) {
            $_var = AttributeVariant::find($item);
            $variants[] = [
                'id' => $_var->id,
                'name' => $_var->name,
                'image' => empty($_var->image->file) ? '' : $_var->getImage(),
            ];
        }

        //Сортировка по имени $variants
        $_count = count($variants);
        for ($i = 0; $i < $_count; $i++) {
            for ($j = 0; $j < $_count - $i - 1; $j++) {
                if (strcasecmp($variants[$j]['name'], $variants[($j + 1)]['name']) >= 0) {
                    $p = $variants[$j];
                    $variants[$j] = $variants[$j + 1];
                    $variants[$j + 1] = $p;
                }
            }
        }

        $result = [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'isVariant' => true,
            'variants' => $variants
        ];

        return $result;
    }

    ////ТЕГИ
    ///
    public function TagsByProducts(array $product_ids)
    {
        return Tag::whereHas('products', function ($query) use ($product_ids) {
            $query->whereIn('id', $product_ids);
        })->get();
    }

    //////


    ///КУПОНЫ И СКИДКИ

    public function getCoupon(string $code, int $user_id = null): ?Coupon
    {
        if (is_null($user_id)) $user_id = Auth::guard('user')->user()->id;

        $coupon = Coupon::where('code', $code)
            ->where('user_id', $user_id)
            ->where('started_at', '<', Carbon::now())
            ->where('finished_at', '>', Carbon::now())
            ->where('status', Coupon::NEW)
            ->first();
        if (!empty($coupon)) return $coupon;
        return null;
    }


    ///КАТЕГОРИИ

    private function CategoriesForSearch(Category $category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'code' => '',
            'image' => !is_null($category->icon) ? $category->icon->getUploadUrl() : '',
            'price' => '',
            'url' => route('shop.category.view', $category->slug),
        ];
    }

    public function getRootCategories()
    {
        return Category::where('parent_id', null)->orderBy('_lft')->get();
    }

    ///ТОВАРЫ

    private function ProductsForSearch(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'image' => !is_null($product->photo) ? $product->photo->getThumbUrl('thumb') : '',
            'price' => number_format($product->getLastPrice(), 0, ' ', ','),
            'url' => route('shop.product.view', $product->slug),
        ];
    }


    private function avto_replace(string $str): string
    {
        $output = '';
        $search_ru = [
            "й", "ц", "у", "к", "е", "н", "г", "ш", "щ", "з", "х", "ъ",
            "ф", "ы", "в", "а", "п", "р", "о", "л", "д", "ж", "э",
            "я", "ч", "с", "м", "и", "т", "ь", "б", "ю",
        ];
        $search_en = [
            "q", "w", "e", "r", "t", "y", "u", "i", "o", "p", "[", "]",
            "a", "s", "d", "f", "g", "h", "j", "k", "l", ";", "'",
            "z", "x", "c", "v", "b", "n", "m", ",", ".",
        ];

        for ($i = 0; $i < mb_strlen($str); $i++) {
            $char = mb_substr($str, $i, 1);
            $key = array_search($char, $search_ru);
            if ($key !== false) {
                $output .= $search_en[$key];
            } else {
                $key = array_search($char, $search_en);
                $output .= $search_ru[$key];
            }
        }
        return $output;
    }

    public function PageBySlug(string $slug): Page
    {
        return Page::where('slug', $slug)->where('published', true)->firstOrFail();
    }

    public function getMapData(): array
    {
        $trader = Trader::where('default', true)->first();

        return array_map(function (Storage $storage) use ($trader) {
            return [
                'latitude' => $storage->latitude,
                'longitude' => $storage->longitude,
                'iconCaption' => $trader->name, //$trader->organization->short_name,
                'balloonContent' => $storage->address,
            ];
        }, Storage::getModels());

        /*
         [
         [
             'latitude' => 54.737798,
             'longitude' => 20.477079,
             'iconCaption' => 'NORDI HOME',
             'balloonContent' => 'Советский проспект 103А корпус 1'
         ],
         [
             'latitude' => 54.678130,
             'longitude' => 20.495324,
             'iconCaption' => 'NORDI HOME',
             'balloonContent' => 'ул. Батальная 18, 2 этаж'
         ],
     ];*/
    }

    public function getPromotionBySlug($slug): Promotion
    {
        return Promotion::where('slug', $slug)->where('published', true)->firstOrFail();
    }

    public function getProdAttributes(Product $product): array
    {
        $productAttributes = [];
        foreach ($product->prod_attributes as $attribute) {
            $value = $attribute->Value();
            if (is_array($value)) {
                $value = implode(', ', array_map(function ($id) use ($attribute) {
                    return $attribute->getVariant((int)$id)->name;
                }, $attribute->Value()));
            }
            $productAttributes[$attribute->group->name][] = [
                'name' => $attribute->name,
                'value' => $value,
            ];
        }
        return $productAttributes;
    }

    public function getGroupBySlug(string $slug): Group
    {
        return Group::where('slug', $slug)->firstOrFail();
    }

}
