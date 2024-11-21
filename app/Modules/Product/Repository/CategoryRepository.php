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

    public function getTree(int $parent_id = null)
    {
        if (is_null($parent_id)) return Category::defaultOrder()->withDepth()->get()->toTree();
        return Category::defaultOrder()->withDepth()->descendantsOf($parent_id)->toTree();
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
}
