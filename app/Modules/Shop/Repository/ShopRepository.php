<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

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
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Tag;
use App\Modules\Product\Repository\ModificationRepository;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\Deprecated;

class ShopRepository
{

    private Web $web;
    protected ?User $user;

    public function __construct(Settings $settings)
    {
        $this->web = $settings->web;

        if (Auth::guard('user')->check()) {
            $this->user = Auth::guard('user')->user();
        } else {
            $this->user = null;
        }
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
    public function ProductsByCategory(Category $category)
    {
        $lft = $category->_lft;
        $rgt = $category->_rgt;

        $query = Product::where('published', true) //Опубликован AND
            ->where(function ($query) use ($lft, $rgt) { //Категории входят в выбранную AND
                $query->whereHas('category', function ($query) use ($lft, $rgt) {
                    $query->where('_lft', '>=', $lft)->where('_rgt', '<=', $rgt);
                })->orWhereHas('categories', function ($query) use ($lft, $rgt) {
                    $query->where('_lft', '>=', $lft)->where('_rgt', '<=', $rgt);
                });
            })->where(function ($query) { //Либо не содержит модификаций, либо Является базовым товаром для модификации
                $query->doesntHave('modification')->orHas('main_modification');
            });

        return $query->get();
    }

    ////КАТЕГОРИИ
    ///



    public function getChildren(int $parent_id = null): Arrayable
    {

        return Category::defaultOrder()->where('parent_id', $parent_id)->get()->map(function (Category $category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image' => $category->getImage('catalog'),
            ];
        });
    }

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
            if (is_array($item)) {
                $variant_ids = array_merge($variant_ids, $item);
            } else {
                $variant_ids[] = $item;
            }
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
            'image' => $product->getImage('card'),
            'price' => number_format($product->getPrice(), 0, ' ', ','),
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



    //Product to Array для Frontend

    public function ProductToArrayCard(Product $product): array
    {
        return [
            'id' => $product->id,
            'code' => $product->code,
            'name' => is_null($product->modification) ? $product->name : $product->modification->name,
            'slug' => $product->slug,
            'has_promotion' => $product->hasPromotion(),
            'is_new' => $product->isNew(),
            'is_wish' => !is_null($this->user) && $product->isWish($this->user->id),
            'is_sale' => $product->isSale(),
            'rating' => $product->current_rating,
            'count_reviews' =>$product->countReviews(),
            'price' => $product->getPrice(false, $this->user),
            'price_promotion' => $product->hasPromotion() ? $product->promotion()->pivot->price : 0,
            'images' => [
                'catalog' => $product->getImageData('catalog'),
            ],
            'images-next' => [
                'catalog' => $product->getImageNextData('catalog'),
            ],
            'modification' => is_null($product->modification) ? null : $this->ModificationToArray($product->modification),

        ];
    }

    private function ModificationToArray(Modification $modification): array
    {
        $attributes = [];
        foreach ($modification->prod_attributes as $attribute) {
            $attributes[$attribute->id] = [
                'name' => $attribute->name,
                'image' => $attribute->getImage(),
            ];
        }

        foreach ($modification->products as $product) {
            if ($product->isSale()) {
                $values = json_decode($product->pivot->values_json, true);
                foreach ($values as $attr_id => $variant_id) {
                    //    $variants[] = $product->getProdAttribute($attr_id)->getVariant($variant_id)->name;
                    $variant_name = $product->getProdAttribute($attr_id)->getVariant($variant_id)->name;
                    $attributes[$attr_id]['products'][$variant_name][] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'image' => $product->miniImage(),
                        //  'variants' => $variants,
                    ];
                }
            }
        }
      //  dd($attributes);
        return $attributes;
        //dd($attributes);
        /*
        return  [
            'attributes' => array_map(function (Attribute $attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'image' => $attribute->getImage(),
                    'variants' => $attribute->variants()->get()->map(function (AttributeVariant $variant) {
                        return [
                            'id' => $variant->id,
                            'name' => $variant->name,
                            'image' => $variant->getImage(),
                        ];
                    })->toArray(),
                ];
            }, $modification->prod_attributes),
            'products' => $modification->products()->get()->map(function (Product $product) {
                $values = json_decode($product->pivot->values_json, true);

                $variants = [];
                foreach ($values as $attr_id => $variant_id) {
                    $variants[] = $product->getProdAttribute($attr_id)->getVariant($variant_id)->name;
                }
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->miniImage(),
                    'variants' => $variants,
                ];
            })->toArray(),
        ];
        */
    }
}
