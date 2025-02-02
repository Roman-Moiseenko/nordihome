<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Category;
use Illuminate\Http\Request;
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
        return $category;
    }

    public function setInfo(Request $request, Category $category): Category
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
        $category->save();

        $category->saveImage($request->file('image'), $request->boolean('image_clear'));
        $category->saveIcon($request->file('icon'), $request->boolean('icon_clear'));
        /*


        if ($request['image-clear'] == 'delete') {
            $category->image->delete();
            $category->refresh();
        }
        if ($request['icon-clear'] == 'delete') {
            $category->icon->delete();
            $category->refresh();
        }

        $this->image($category, $request->file('image'));
        $this->icon($category, $request->file('icon'));
*/

        return $category;
    }

    public function destroy(Category $category): void
    {
        if (!empty($category->allProducts())) {
            Category::destroy($category->id);
        } else {
            throw new \DomainException('Нельзя удалить категорию с товарами');
        }
    }

    public function delete(Category $category)
    {
        if (count($category->children) == 0) {
            Category::destroy($category->id);
        } else {
            throw new \DomainException('Нельзя удалить категорию с подкатегориями');
        }
    }


    public function image(Category $category, $file): void
    {
        $category->saveImage($file);
    }


    public function icon(Category $category, $file): void
    {
        $category->saveIcon($file);
    }

}
