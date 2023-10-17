<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Category;
use App\UseCases\Uploads\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryService
{
    private UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    public function register(Request $request): Category
    {
        $category = Category::register(
            $request['name'],
            $request['parent_id'] ?? null,
            $request['slug'] ?? '',
            $request['title'] ?? '',
            $request['description'] ?? ''
        );

        if (!empty($request->file('image')))
            $category->setImage($this->uploadService->singleReplace($request->file('image'), $category));
        if (!empty($request->file('icon')))
            $category->setIcon($this->uploadService->singleReplace($request->file('image'), $category));

        $category->save();
        return $category;
    }

    public function update(Request $request, Category $category): Category
    {
        $category->name = $request['name'];
        $category->description = $request['description'];
        $category->title = $request['title'];
        if ($category->slug != $request['slug']) {
            $category->slug = empty($request['slug']) ? Str::slug($request['name']) : $request['slug'];
        }

        if (!empty($request->file('image')))
            $category->setImage($this->uploadService->singleReplace($request->file('image'), $category));
        if (!empty($request->file('icon')))
            $category->setIcon($this->uploadService->singleReplace($request->file('image'), $category));

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
}
