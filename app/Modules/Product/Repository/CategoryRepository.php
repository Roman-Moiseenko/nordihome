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
        return !is_null(Category::find($id));
    }

    public function existAndGet(int $id): ?Category
    {
        return Category::find($id);
    }

    //Формируем массив для передачи json ч/з ajax списка доступных атрибутов для товара
    // если атрибут назначен, то $complete = true и $block сдержит значение атрибута
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
            /** @var Attribute $attr_obj */
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

    //TODO Ускорить для Shop
    public function getTree(int $parent_id = null)
    {
        $query = Category::defaultOrder()->withDepth();
        if (is_null($parent_id)) {
            $categories = $query->get();
        } else {
            $categories = $query->descendantsOf($parent_id);
        }
        return $categories->map(function (Category $category) {
            $category->image_url = $category->getImage();
            $category->icon_url = $category->getIcon();
            return $category;
        })->toTree();
        /*
        if (is_null($parent_id)) {
            $categories = Category::defaultOrder()->withDepth()->get()->toTree();
        } else {
            $categories = Category::defaultOrder()->withDepth()->descendantsOf($parent_id)->toTree();
        } */
        // $categories;
    }

    //TODO Ускорить для Shop
    public function getTreeForShop(int $parent_id = null)
    {

        return Category::defaultOrder()->where('parent_id', null)->get()->map(function (Category $category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image' => $category->getImage('catalog'),
            ];
        });

        if (is_null($parent_id)) {
            $categories = $query->get();
        } else {
            $categories = $query->descendantsOf($parent_id);
        }
        return $categories->toTree();

    }

    /**
     * Возвращает все категории с учетом глубины
     * @return mixed
     */
    public function withDepth()
    {
        return Category::defaultOrder()->withDepth()->getModels();
    }

    public function search(string $search, int $take = 10)
    {
        $query = Category::orderBy('name')->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        });
        return $query->take($take)->get();
    }

    public function forFilters(): array
    {
        return array_map(function (Category $category) {
            $_depth = str_repeat('-', $category->depth);
            return [
                'id' => $category->id,
                'name' => trim($_depth . ' ' . $category->name),
            ];
        }, $this->withDepth());
    }

    public function CategoryWith(Category $category): array
    {
        return array_merge($category->toArray(), [
            'image' => $category->getImage(),
            'icon' => $category->getIcon(),
            'children' => $this->getTree($category->id),
            'attributes' => [
                'parent' => $this->attributeForCategory($category->parent_attributes()),
                'self' => $this->attributeForCategory($category->prod_attributes()->getModels()),
            ],
            'products' => $category->products()->get()->map(function (Product $product) {
                return array_merge($product->toArray(), [
                    'image' => $product->miniImage(),
                ]);
            }),
        ]);
    }

    private function attributeForCategory(array $attributes): array
    {
        return array_map(function (Attribute $attribute) {
            return [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'group' => $attribute->group->name,
                'filter' => $attribute->filter,
                'type_text' => $attribute->typeText(),
                'image' => $attribute->getImage(),
            ];
        }, $attributes);
    }
}
