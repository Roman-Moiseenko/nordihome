<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Brand;
use App\UseCases\Photo\UploadSinglePhoto;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class BrandService
{
    private UploadSinglePhoto $uploadSinglePhoto;

    public function __construct(UploadSinglePhoto $uploadSinglePhoto)
    {
        $this->uploadSinglePhoto = $uploadSinglePhoto;
    }

    public function register(Request $request): Brand
    {
        $brand = Brand::register(
            $request['name'],
            $request['description'],
            $request['url'],
            $request['sameAs']
        );

        //Фото
        if (!empty($request->file('file')))
            $this->uploadSinglePhoto->savePhoto($request->file('file'), $brand);

        return $brand;
    }

    public function update(Request $request, Brand $brand)
    {

        return $brand;
    }

    public function delete(Brand $brand)
    {
    }

    ////
/*
    public function setPhoto(UploadedFile $file, Brand $brand): void
    {
        $path = $brand->uploads . $brand->id . '/';
        if (!file_exists(public_path() . '/' . $path)) {
            mkdir(public_path() . '/' . $path, 0777, true);
        }
        $file->move($path, $file->getClientOriginalName());
        if (!empty($brand->photo)) {
            unlink(public_path() . $brand->photo);
        }
        $brand->photo = '/' . $path . $file->getClientOriginalName();
        $brand->save();
    }    */
}
