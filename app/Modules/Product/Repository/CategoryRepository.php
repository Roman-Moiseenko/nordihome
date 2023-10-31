<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Category;
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

    public function relationAttributes(array $categories_id): array
    {
        $result = [];
        $array_cats = []; //Id выбранных категорий и всех родительских (до верхнего уровня)
        foreach ($categories_id as $category_id) {
            if (!is_null($category = $this->existAndGet((int)$category_id))) {

                $lft = $category->_lft;
                $rgt = $category->_rgt;
                $_t = Category::orderBy('id')->where('_lft', '<=', $lft)->where('_rgt', '>=', $rgt)->pluck('id')->toArray();
                $array_cats = array_merge($array_cats, $_t);
            }
        }
        $array_cats = array_unique($array_cats);
        $categories = Category::orderBy('id')->whereIn('id', $array_cats)->get();
        foreach($categories as $category) { //Собираем атрибуты по всем категориям
            foreach ($category->attributes as $attribute) {
                $result[$attribute->id] = [
                    'id' => $attribute->id,
                    'attribute' => $attribute->name,
                    'group' => $attribute->group->name,
                ];
            }
        }
        return $result;
    }
}
