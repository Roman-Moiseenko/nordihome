<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Entity\Photo;
use App\Modules\Product\Entity\Brand;
use App\UseCases\Uploads\UploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class BrandService
{
    public function register(Request $request): Brand
    {
        $brand = Brand::register(
            $request['name'],
            $request['description'] ?? '',
            $request['url'] ?? ''
        );

        if (!empty($request['sameAs']) && is_array($request['sameAs']))
            $brand->setSameAs($request['sameAs']);

        $this->photo($brand, $request->file('file'));
        $brand->save();

        return $brand;
    }

    public function update(Request $request, Brand $brand)
    {
        $brand->name = $request['name'];
        $brand->description = $request['description'];
        $brand->url = $request['url'];
        $brand->setSameAs($request['sameAs']);
        $brand->update();
        if ($request['file-clear'] == 'delete') {
            $brand->photo->delete();
            $brand->refresh();
        }
        $this->photo($brand, $request->file('file'));

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

    public function photo(Brand $brand, $file): void
    {
        if (empty($file)) return;
        if (!empty($brand->photo)) {
            $brand->photo->newUploadFile($file);
        } else {
            $brand->photo()->save(Photo::upload($file));
        }
        $brand->refresh();
    }

}
