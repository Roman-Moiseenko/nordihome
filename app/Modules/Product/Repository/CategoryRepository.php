<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

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
        foreach($categories as $category) { //Собираем атрибуты по всем категориям
            foreach ($category->attributes as $attribute) {
                if (!isset($result[$attribute->id])) {
                    if (!is_null($product->getProdAttribute($attribute->id))) {
                        //TODO получить Values и заполнить block значениями, и поставить отметку complete в true
                        //Обработка tom-select ... возможно поставить задержку
                    }
                    $result[$attribute->id] = [
                        'id' => $attribute->id,
                        'attribute' => $attribute->name,
                        'group' => $attribute->group->name,
                        'block' => view('admin.product.product.blocks.attribute', ['attribute' => $attribute])->render(),
                        'id_tom_select' => 'select-variant-' . $attribute->id,
                        'complete' => false,
                    ];
                }
            }
        }
        return $result;
    }

}
