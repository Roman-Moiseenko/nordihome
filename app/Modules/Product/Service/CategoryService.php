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
            $request['name'],
            $request['parent_id'] ?? null,
            $request['slug'] ?? '',
            $request['title'] ?? '',
            $request['description'] ?? ''
        );

        $this->image($category, $request->file('image'));
        $this->icon($category, $request->file('icon'));

        $category->save();
        return $category;
    }

    public function update(Request $request, Category $category): Category
    {
        $category->name = $request['name'];
        if (isset($request['parent_id'])) {
            $category->parent_id = (int)$request['parent_id'] == 0 ? null : (int)$request['parent_id'];
        }
        $category->description = $request['description'] ?? '';
        $category->title = $request['title'] ?? '';
        if ($category->slug != $request['slug']) {
            $category->slug = empty($request['slug']) ? Str::slug($request['name']) : $request['slug'];
        }

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

        $category->save();
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
        if (empty($file)) return;
        $category->image->newUploadFile($file, 'image');
        $category->refresh();
    }


    public function icon(Category $category, $file): void
    {
        if (empty($file)) return;
        $category->icon->newUploadFile($file, 'icon');
        $category->refresh();
    }
}
