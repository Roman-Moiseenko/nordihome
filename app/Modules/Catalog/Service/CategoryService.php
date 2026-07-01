<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Service;

use App\Modules\Base\Helpers\CacheHelper;
use App\Modules\Catalog\Infrastructure\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryService
{

    public function register(Request $request): Category
    {
        $category = Category::register(
            $request->string('name')->trim()->value(),
            $request['parent_id'] ?? null,
            $request->string('slug')->trim()->value(),
            $request->string('title')->trim()->value(),
            $request->string('description')->trim()->value()
        );
        $category->saveImage($request->file('image'));
        $category->saveIcon($request->file('icon'));

        $category->save();
        $this->clearCache();
        return $category;
    }

    public function setInfo(Request $request, Category $category): void
    {
        $category->name = $request->string('name')->trim()->value();
        if ($request->has('parent_id')) {
            $category->parent_id = (int)$request['parent_id'] == 0 ? null : (int)$request['parent_id'];
        }
        $meta = [
            'title' => $request->string('title')->trim()->value(),
            'description' => $request->string('description')->trim()->value(),
        ];
        $category->meta = $meta;
        $new_slug = $request->string('slug')->trim()->value();

        if ($category->slug != $new_slug) {
            if (empty($new_slug)) {
                $new_slug = Str::slug($category->name);
                if (!empty(Category::where('slug', $new_slug)->first())) {
                    if (!is_null($category->parent_id)) {
                        $new_slug .= '-' . $category->parent->slug;
                    } else {
                        $new_slug .= Str::random(4);
                    }
                }
            }
            $category->slug = $new_slug;
        }

        $category->svg = $request->string('svg')->trim()->value();

        $category->save();
        $category->saveImage($request->file('image'), $request->boolean('clear_image'));
        $category->saveIcon($request->file('icon'), $request->boolean('clear_icon'));

        $this->clearCache();
    }

    public function delete(Category $category): void
    {
        if (count($category->children) == 0) {
            Category::destroy($category->id);
            $this->clearCache();
        } else {
            throw new \DomainException('Нельзя удалить категорию с подкатегориями');
        }
    }

    private function clearCache(): void
    {
        Cache::put(CacheHelper::MENU_CATEGORIES, '', -1);
        Cache::put(CacheHelper::MENU_TREES, '', -1);
    }
}
