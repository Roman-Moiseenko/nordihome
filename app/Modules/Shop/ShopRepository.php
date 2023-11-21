<?php
declare(strict_types=1);

namespace App\Modules\Shop;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;

class ShopRepository
{

    public function getProductBySlug($slug): Product
    {
        if (is_numeric($slug)) return Product::findOrFail($slug);
        return Product::where('slug', '=', $slug)->firstOrFail();
    }
    public function ProductsForSearch(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'image' => !is_null($product->photo) ? $product->photo->getThumbUrl('thumb') : '',
            'price' => number_format($product->lastPrice->value, 0, ' ', ','),
            'url' => route('shop.product.view', $product->slug),
        ];
    }


    public function searchProduct(string $search, int $take = 10, array $include_ids = [], bool $isInclude = true)
    {
        $search_back = $this->avto_replace($search);

        $query = Product::orderBy('name')->where(function ($query) use ($search, $search_back) {
            $query->where('code_search', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search_back}%");
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

    public function ProductsByCategory(int $id)
    {
        //TODO Только опубликованные
        $products = Product::where('main_category_id', '=', $id)->OrWhere(function ($query) use ($id) {
            $query->whereHas('categories', function ($_query) use ($id) {
                $_query->where('category_id', $id);
            });
        })->get();
        return $products;
    }

    ////КАТЕГОРИИ
    ///
    public function CategoryBySlug($slug): Category
    {
        if (is_numeric($slug)) return Category::findOrFail($slug);
        return Category::where('slug', '=', $slug)->firstOrFail();
    }

    public function searchCategory(string $search, int $take = 10)
    {
        $query = Category::orderBy('name')->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        });
        return $query->take($take)->get();
    }
    public function CategoriesForSearch(Category $category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'code' => '',
            'image' => !is_null($category->icon) ? $category->icon->getUploadUrl() : '',
            'price' => '',
            'url' => route('shop.category.view', $category),
        ];
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
}
