<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Brand;
use App\UseCases\Uploads\UploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class BrandService
{
    private UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function register(Request $request): Brand
    {
        $brand = Brand::register(
            $request['name'],
            $request['description'],
            $request['url'],
            $request['sameAs']
        );

        if (!empty($request->file('file')))
            $brand->setPhoto($this->uploadService->singleReplace($request->file('file'), $brand));

        return $brand;
    }

    public function update(Request $request, Brand $brand)
    {
        $brand->name = $request['name'];
        $brand->description = $request['description'];
        $brand->url = $request['url'];
        $brand->setSameAs($request['sameAs']);
        if (!empty($request->file('file')))
            $brand->setPhoto($this->uploadService->singleReplace($request->file('file'), $brand));
        $brand->save();
        return $brand;
    }

    public function delete(Brand $brand)
    {
        if (empty($brand->products())) {
            Brand::destroy($brand->id);
        } else {
            throw new \DomainException('Нельзя удалить бренд с товарами');
        }
    }

}
