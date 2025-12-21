<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Base\Entity\Photo;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\Page;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeProduct;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Review;
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
use LaravelIdea\Helper\App\Modules\Product\Entity\_IH_Tag_C;
use function Sodium\add;

class ShopRepository
{

    private Web $web;
    protected ?User $user;
    private Settings $settings;
    private SlugRepository $slugs;

    public function __construct(Settings $settings, SlugRepository $slugs)
    {
        $this->web = $settings->web;

        if (Auth::guard('user')->check()) {
            $this->user = Auth::guard('user')->user();
        } else {
            $this->user = null;
        }
        $this->settings = $settings;
        $this->slugs = $slugs;
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

    public function filter(array $request, array $product_ids)
    {
        $query = Product::orderByDesc('priority');

        $query = match ($request['order'] ?? null) {
            'price-down' => $query->orderBy('current_price', 'desc'),
            'price-up' => $query->orderBy('current_price', 'asc'),
            'rating' => $query->orderBy('current_rating', 'asc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('name', 'asc'),
        };

        //Теги
        if (!empty($tag = $request['tag_id'] ?? null)) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('id', $tag);
            });
        }
        //Бренд
        if (!empty($brands = $request['brands'] ?? null)) $query->whereIn('brand_id', $brands);
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
        foreach ($request as $key => $item) {
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
                    $query->where(function ($query) use ($item) {
                        $query
                            ->where(function ($query) use ($item) {
                                $query->doesntHave('modification')
                                    ->whereHas('prod_attributes', function ($query) use ($item) {
                                        $this->checkVariantsQuery($query, $item); //
                                    });

                            })
                            ->orWhere(function ($query) use ($item) {
                                $query->whereHas('modification', function ($query) use ($item) {
                                    $query->whereHas('products', function ($query) use ($item) {
                                        $query->where('not_sale', false)->whereHas('prod_attributes', function ($query) use ($item) {
                                            $this->checkVariantsQuery($query, $item);
                                        });
                                    });
                                });
                            });

                    });
                }
            }
        }
        return $query->whereIn('id', $product_ids);
    }

    private function checkVariantsQuery(&$query, $item): void
    {
        if (is_array($item)) {
            foreach ($item as $k => $_od) {
                if ($k == 0) {
                    $query->whereJsonContains('value', (int)$_od);
                } else {
                    $query->orWhereJsonContains('value', (int)$_od);
                }
            }
        } else {
            $query->whereJsonContains('value', (int)$item);
        }
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
    public function ProductsByCategory(Category $category = null)
    {
        if (is_null($category)) {
            $lft = Category::get()->min('_lft');
            $rgt = Category::get()->max('_rgt');

        } else {
            $lft = $category->_lft;
            $rgt = $category->_rgt;
        }

        $query = Product::where('published', true) //Опубликован AND
        ->where(function ($query) use ($lft, $rgt) { //Категории входят в выбранную AND
            $query->whereHas('category', function ($query) use ($lft, $rgt) {
                $query->where('_lft', '>=', $lft)->where('_rgt', '<=', $rgt);
            })->orWhereHas('categories', function ($query) use ($lft, $rgt) {
                $query->where('_lft', '>=', $lft)->where('_rgt', '<=', $rgt);
            });
        })->where(function ($query) { //Либо не содержит модификаций, либо Является базовым товаром для модификации
            $query->doesntHave('modification')->orWhere(function ($query) {
                $query->has('main_modification')->whereHas('main_modification', function ($query) {
                    $query->where('not_sale', false);
                });
            });
        });

        return $query->get();
    }

    ////КАТЕГОРИИ
    ///

    public function getChildren(int $parent_id = null): Arrayable
    {
        return Category::defaultOrder()->where('parent_id', $parent_id)
            ->where('slug', '<>', Category::NO_PARSE)
            ->get()
            ->map(function (Category $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image' => $category->getImage(),
                ];
            });
    }

    public function getChildrenParser(int $parent_id = null): Arrayable
    {
        return CategoryParser::defaultOrder()->where('parent_id', $parent_id)
            ->where('active', true)
            ->get()
            ->map(function (CategoryParser $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image' => $category->getImage(),
                    'children' => $category->children()->get()->map(fn(CategoryParser $child) => [
                        'id' => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                    ])->toArray(),
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
    ///PARSER

    public function ParserProductsByCategory(CategoryParser $category = null)
    {
        if (is_null($category)) {
            $lft = CategoryParser::get()->min('_lft');
            $rgt = CategoryParser::get()->max('_rgt');

        } else {
            $lft = $category->_lft;
            $rgt = $category->_rgt;
        }

        return ProductParser::where('availability', true) //Опубликован AND
        ->where(function ($query) use ($lft, $rgt) { //Категории входят в выбранную AND
            $query->whereHas('categories', function ($query) use ($lft, $rgt) {
                $query->where('_lft', '>=', $lft)->where('_rgt', '<=', $rgt);
            });
        });
    }
    public function ParserProductToArrayCard(ProductParser $product): array
    {
        return array_merge($this->ParserProductToArray($product), [
            'images' => [
                'catalog' => $product->product->getImageData('catalog'),
            ],
            'images-next' => [
                'catalog' => $product->product->getImageNextData('catalog'),
            ],
        ]);
    }
    private function ParserProductToArray(ProductParser $product): array
    {
        return [
            'id' => $product->id,
            'code' => $product->product->code,
            'name' => $product->product->name,
            'slug' => $product->slug,
            'price' => $product->price_sell * $this->settings->parser->parser_coefficient,
            'image' => [
                'src' => $product->product->getImage('card'),
            ],


        ];
    }

    public function ParserProductToArrayView(ProductParser $product): array
    {
        return array_merge($this->ParserProductToArray($product), [
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'description' => $product->product->description,
            'short' => $product->product->short,

            'gallery' => $product->product->photos()->get()->map(function (Photo $photo) {
                return [
                    'mini' => $photo->getThumbUrl('mini'),
                    'src' => $photo->getThumbUrl('card'),
                    'alt' => $photo->alt,
                    'title' => $photo->alt,
                    'description' => $photo->description,
                ];
            }),
            'categories' => $product->categories()->get()->map(fn(CategoryParser $category) => [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->name,
            ])->toArray(),


            'dimensions' => [
                'width' => $product->product->dimensions->width,
                'height' => $product->product->dimensions->height,
                'depth' => $product->product->dimensions->depth,
                'weight' => $product->product->weight(),
                'volume' => $product->product->volume(),
                'captions' => Dimensions::CAPTION_TYPES[$product->product->dimensions->type],
            ],
            'local' => $product->product->local,
            'delivery' => $product->product->delivery,

        ]);

    }

    /// <=====

    ////АТРИБУТЫ
    ///
    public function AttributeCommon(array $categories_id, array $product_ids): array
    {
        $attrs_cat = Attribute::whereHas('categories', function ($query) use ($categories_id) {
            $query->whereIn('category_id', $categories_id);
        })->pluck('id')->toArray();

        $attrs_prod = Attribute::whereHas('products', function ($query) use ($product_ids) {
            $query->whereIn('id', $product_ids);
        })->pluck('id')->toArray();

        //Включая и товары из модификации
        $product_ids = array_merge(
            $product_ids,
            Product::whereHas('modification', function ($query) use ($product_ids) {
                $query->whereHas('products', function ($query) use ($product_ids) {
                    $query->whereIn('id', $product_ids);
                });
            })->pluck('id')->toArray()
        );

        $_attr_intersect = array_intersect($attrs_cat, $attrs_prod); //Общие id атрибутов для товаров и категорий

        $attributes = Attribute::whereIn('id', $_attr_intersect)->where('filter', '=', true)->orderBy('group_id')->get();
        $prod_attributes = [];

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {  //Заполняем варианты и мин.и макс. значения из возможных для данных товаров
            if ($attribute->isNumeric()) $prod_attributes[] = $this->getNumericAttribute($attribute, $product_ids);
            if ($attribute->isVariant()) {

                $prod_attributes[] = $this->getVariantAttribute($attribute, $product_ids);
            }
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

    private function getVariantAttribute(Attribute $attribute, array $product_ids): array
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
            //if (is_null($_var)) dd($item);
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
    public function TagsByProducts(array $product_ids): Arrayable
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

    private function CategoriesForSearch(Category $category): array
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
        try {
            $productAttributes = [];
            foreach ($product->prod_attributes as $attribute) {
                $value = $attribute->Value();
                if ($attribute->isVariant()) {
                    //if (!is_array($value)) $value[] = $value;
                    if (is_array($attribute->Value())) {
                        $value = implode(', ', array_map(function ($id) use ($attribute) {
                            return $attribute->getVariant((int)$id)->name;
                        }, $attribute->Value()));
                    } else {
                        $value = $attribute->getVariant((int)$attribute->Value())->name;
                    }
                }
                $productAttributes[$attribute->group->name][] = [
                    'name' => $attribute->name,
                    'value' => $value,
                ];
            }
            return $productAttributes;
        } catch (\DomainException $e) {
            \Log::info('getProdAttributes: ' . $product->code);
            return [];
        }

    }


    //Product to Array для Frontend

    public function ProductToArrayCard(Product $product): array
    {
        return array_merge($this->ProductToArray($product), [
            'images' => [
                'catalog' => $product->getImageData('catalog'),
            ],
            'images-next' => [
                'catalog' => $product->getImageNextData('catalog'),
            ],
        ]);
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

        try {
            foreach ($modification->products as $product) {
                if ($product->isSale()) {
                    $values = json_decode($product->pivot->values_json, true);
                    foreach ($values as $attr_id => $variant_id) {
                        $variant_name = $product->getProdAttribute($attr_id)->getVariant($variant_id)->name;
                        $attributes[$attr_id]['products'][$variant_name][] = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'slug' => $product->slug,
                            'image' => $product->miniImage(),
                        ];
                    }
                }
            }
        } catch (\DomainException $e) {
            \Log::info('ModificationToArray: ' . $modification->name . ' ' . $e->getMessage());
        }


        return $attributes;
    }


    public function ProductToArrayView(Product $product): array
    {
        $_product = null;
        $equivalents = [];
        if (!is_null($product->equivalent_product)) {
            $_product = $product;
        } elseif (!is_null($product->modification) && is_null($product->main_modification)) {
            $_product = $product->modification->base_product;
        }
        if (!is_null($_product) && !is_null($_product->equivalent_product)) {
            $equivalents = $_product->equivalent->products()->where('not_sale', false)->get()->map(function (Product $product) {
                return [
                    'slug' => $product->slug,
                    'code' => $product->code,
                    'name' => $product->name,
                    'src' => $product->miniImage(),
                ];
            });
        }

        return array_merge($this->ProductToArray($product), [
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'description' => $product->description,
            'short' => $product->short,
            'quantity' => $product->getQuantitySell(),
            'brand' => [
                'src' => $product->brand->getImage(),
                'name' => $product->brand->name,
            ],
            'gallery' => $product->photos()->get()->map(function (Photo $photo) {
                return [
                    'mini' => $photo->getThumbUrl('mini'),
                    'src' => $photo->getThumbUrl('card'),
                    'alt' => $photo->alt,
                    'title' => $photo->alt,
                    'description' => $photo->description,
                ];
            }),
            'category' => [
                'id' => $product->category->id,
                'slug' => $product->category->slug,
                'name' => $product->category->name,
            ],
            'equivalents' => $equivalents,
            'bonus' => $product->bonus()->get()->map(function (Product $product) {
                $bonus = $this->ProductToListData($product);
                $bonus['discount'] = $product->pivot->discount;
                return $bonus;
            }),
            'series' => is_null($product->series) ? [] : [
                'name' => $product->series->name,
                'products' => $product->series->products()->get()->map(function (Product $product) {
                    return $this->ProductToListData($product);
                }),
            ],

            'related' => $product->related()->get()->map(function (Product $product) {
                return $this->ProductToListData($product);
            }),
            'dimensions' => [
                'width' => $product->dimensions->width,
                'height' => $product->dimensions->height,
                'depth' => $product->dimensions->depth,
                'weight' => $product->weight(),
                'volume' => $product->volume(),
                'captions' => Dimensions::CAPTION_TYPES[$product->dimensions->type],
            ],
            'local' => $product->local,
            'delivery' => $product->delivery,
            'reviews' => $product->reviews()->get()->map(function (Review $review) {
                return [
                    'user_name' => $review->user->fullname->firstname,
                    'rating' => $review->rating,
                    'text' => $review->text,
                    'date' => $review->htmlDate(),
                    'src' => $review->photo == null ? null : $review->getImage('mini')
                ];
            }),

        ]);

    }

    private function ProductToListData(Product $product): array
    {
        return [
            'id' => $product->id,
            'code' => $product->code,
            'name' => is_null($product->modification) ? $product->name : $product->modification->name,
            'slug' => $product->slug,
            'image' => [
                'src' => $product->getImage('card'),
            ],
            'price' => $product->getPrice(false, $this->user),
        ];
    }

    private function ProductToArray(Product $product): array
    {
        return [
            'id' => $product->id,
            'code' => $product->code,
            'name' => is_null($product->modification) ? $product->name : $product->modification->name,
            'slug' => $product->slug,

            'is_new' => $product->isNew(),
            'is_wish' => !is_null($this->user) && $product->isWish($this->user->id),
            'is_sale' => $product->isSale(),
            'rating' => $product->current_rating,
            'count_reviews' => $product->countReviews(),
            'price' => $product->getPrice(false, $this->user),
            'price_previous' => $product->getPrice(true, $this->user),
            'quantity' => $product->getQuantity(),
            'image' => [
                'src' => $product->getImage('card'),
            ],

            'modification' => is_null($product->modification) ? null : $this->ModificationToArray($product->modification),
            'promotion' => [
                'has' => $product->hasPromotion(),
                'price' => $product->hasPromotion() ? $product->promotion()->pivot->price : 0,
                'title' => is_null($product->promotion()) ? null : $product->promotion()->title,
            ],
        ];
    }
}
