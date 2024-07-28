<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Brand;
use Illuminate\Http\Request;

class BrandService
{
    public function register(Request $request): Brand
    {
        $brand = Brand::register(
            $request->string('name')->trim()->value(),
            $request->string('description')->trim()->value(),
            $request->string('url')->trim()->value()
        );

        if (!empty($request['sameAs']) && is_array($request['sameAs']))
            $brand->setSameAs($request['sameAs']);

        $this->photo($brand, $request->file('file'));
        $brand->save();

        return $brand;
    }

    public function update(Request $request, Brand $brand)
    {
        $brand->name = $request->string('name')->trim()->value();
        $brand->description = $request->string('description')->trim()->value();
        $brand->url = $request->string('url')->trim()->value();
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
