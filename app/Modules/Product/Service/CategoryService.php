<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Base\Helpers\CacheHelper;
use App\Modules\Product\Entity\Category;
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
        $category->description = $request->string('description')->trim()->value();
        $category->title = $request->string('title')->trim()->value();
        $new_slug = $request->string('slug')->trim()->value();
        if ($category->slug != $new_slug) {
            $category->slug = empty($new_slug) ? Str::slug($category->name) : $new_slug;
        }

        $category->top_title = $request->string('top_title')->trim()->value();
        $category->top_description = $request->string('top_description')->trim()->value();
        $category->bottom_text = $request->string('bottom_text')->trim()->value();
        $category->data = $request->string('data')->trim()->value();

        $category->save();

        $category->saveImage($request->file('image'), $request->boolean('image_clear'));
        $category->saveIcon($request->file('icon'), $request->boolean('icon_clear'));

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
