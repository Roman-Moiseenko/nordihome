<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryRepository
{
    public function exists(int $id): bool
    {
        try {
            Category::findOrFail($id);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }

    public function existAndGet(int $id): ?Category
    {
        try {
            return Category::findOrFail($id);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function relationAttributes(array $categories_id, int $product_id): array
    {
        /** @var Product $product */
        $product = Product::find($product_id);
        $result = [];

        $array_cats = []; //Id выбранных категорий и всех родительских (до верхнего уровня)
        foreach ($categories_id as $category_id) {
            if (!is_null($category = $this->existAndGet((int)$category_id))) {
                $array_cats = array_merge($array_cats, $category->getParentIdAll());
            }
        }
        $array_cats = array_unique($array_cats);
        $categories = Category::orderBy('id')->whereIn('id', $array_cats)->get();

        $_attr = [];
        foreach ($categories as $category) { //Собираем атрибуты по всем категориям
            foreach ($category->prod_attributes as $attribute) {
                $_attr[] = $attribute->id;
            }
        }
        $_attr = array_unique($_attr);
        foreach ($_attr as $id_attribute) {
            $attr_obj = Attribute::find($id_attribute);
            $value = $product->Value($id_attribute);
            $result[$id_attribute] = [
                'id' => $id_attribute,
                'attribute' => $attr_obj->name,
                'group' => $attr_obj->group->name,
                'block' => view('admin.product.product.blocks.attribute', ['attribute' => $attr_obj, 'value' => $value])->render(),
                'id_tom_select' => 'select-variant-' . $id_attribute,
                'complete' => !is_null($value),
            ];
        }
        /*
        foreach($categories as $category) { //Собираем атрибуты по всем категориям
            foreach ($category->prod_attributes as $attribute) {
                if (!isset($result[$attribute->id])) {
                    //$value = $product->Value($attribute->id);
                    $result[$attribute->id] = [
                        'id' => $attribute->id,
                        'attribute' => $attribute->name,
                        'group' => $attribute->group->name,
                        'block' => view('admin.product.product.blocks.attribute', ['attribute' => $attribute, 'value' => $product->Value($attribute->id)])->render(),
                        'id_tom_select' => 'select-variant-' . $attribute->id,
                        'complete' => !is_null($product->Value($attribute->id)),
                    ];
                }
            }
        }*/


        return $result;
    }


    public function byName(string $name): Category
    {
        return Category::where('name', '=', $name)->first();
    }

    public function getTree(int $parent_id = null)
    {
        if (is_null($parent_id)) return Category::defaultOrder()->get()->toTree();
        return Category::defaultOrder()->descendantsOf($parent_id)->toTree(); //where('parent_id', '=', $parent_id)->get()->toTree();
    }

    public function withDepth()
    {
        return Category::defaultOrder()->withDepth()->get();
    }

    public function search(string $search, int $take = 10)
    {
        $query = Category::orderBy('name')->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        });
        return $query->take($take)->get();
    }

    public function toShopForSearch(Category $category)
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
            'url' => route('shop.category.view', $category),
            'image' => !is_null($category->image) ? $category->image->getUploadUrl() : '',
            'products' => count($category->products),
            'children' => $children,
        ];
    }

    //private function _recurseCategory(Category $category)
}
