<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Category;
use App\UseCases\Photo\UploadSinglePhoto;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CategoryService
{
    private UploadSinglePhoto $uploadSinglePhoto;

    public function __construct(UploadSinglePhoto $uploadSinglePhoto)
    {
        $this->uploadSinglePhoto = $uploadSinglePhoto;
    }
    public function register(Request $request)
    {
        $category = Category::register(
            $request['name'],
            $request['parent_id'] ?? null,
            $request['slug'] ?? '',
            $request['title'] ?? '',
            $request['description'] ?? ''
        );
        if (!empty($request->file('image')))
            $this->uploadSinglePhoto->savePhoto($request->file('image'), $category, 'setImage');
            //$this->setImage($request->file('image'), $category);
        if (!empty($request->file('icon')))
            $this->uploadSinglePhoto->savePhoto($request->file('image'), $category, 'setIcon');
            //$this->setIcon($request->file('icon'), $category);

        $category->save();
        return $category;
    }


    public function update(Request $request, Category $category)
    {

    }
}
