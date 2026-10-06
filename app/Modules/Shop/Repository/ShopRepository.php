<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

use App\Modules\Auth\Infrastructure\Models\User;
use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Catalog\Infrastructure\Models\AttributeProduct;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Modification;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;
use Illuminate\Support\Facades\Auth;

class ShopRepository
{

    private Web $web;
    protected ?User $user;
    private Settings $settings;
    private SlugRepository $slugs;

    public function __construct(Settings $settings, SlugRepository $slugs)
    {
        $this->web = $settings->web;

        if (Auth::guard('web')->check()) {
            $this->user = Auth::guard('web')->user();
        } else {
            $this->user = null;
        }
        $this->settings = $settings;
        $this->slugs = $slugs;
    }
/*
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
*/
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

    public function getTree(int $parent_id = null)
    {
        if (is_null($parent_id)) return Category::defaultOrder()->get()->toTree();
        return Category::defaultOrder()->descendantsOf($parent_id)->toTree();
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


    ///ТОВАРЫ
/*
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

*/
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

    private function ModificationToArray(Modification $modification): array
    {
        $attributes = [];
        foreach ($modification->prod_attributes as $attribute) {
            $attributes[$attribute->id] = [
                'name' => $attribute->name,
                'image' => GetPhotoStatic::get('catalog.attribute', $attribute->id), //FixMe $attribute->getImage(),
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
                            'image' => GetPhotoStatic::gallery('catalog.product', $product->id, 'mini'),
                        ];
                    }
                }
            }
        } catch (\DomainException $e) {
            \Log::info('ModificationToArray: ' . $modification->name . ' ' . $e->getMessage());
        }


        return $attributes;
    }

/*
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
                'src' => GetPhotoStatic::get('catalog.brand', $product->brand_id), //FixMe
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


            'is_wish' => !is_null($this->user) && $product->isWish($this->user->id), //В избранном у клиента
            'is_sale' => $product->isSale(), //Доступен для продажи
            'rating' => $product->current_rating, //Рейтинг по отзывам
            'count_reviews' => $product->countReviews(), //Кол-во отзывов

            //'price_reduced' => $product->price_reduced, //Цена снижена
            'price' => $product->getPrice(false, $this->user), //Тек.цена
            'price_previous' => $product->getPrice(true, $this->user), //Пред.цена
            'quantity' => $product->getQuantity(),
            'image' => [
                'src' => $product->getImage('card'),
            ],
            'priority' => $product->priority, //Приоритетный показ

            'is_new' => $product->isNew(), //Товар новый
            'reduced' => $product->price_reduced, //Цена снижена
            'only_on_order' => $product->only_on_order, //Только под заказ

            'modification' => is_null($product->modification) ? null : $this->ModificationToArray($product->modification),
            'promotion' => [  //Акции
                'has' => $product->hasPromotion(), //Акционные
                'price' => $product->hasPromotion() ? $product->promotion()->pivot->price : 0, //Акционная цена
                'title' => is_null($product->promotion()) ? null : $product->promotion()->name, //Название акции
            ],
        ];
    }
*/
}
