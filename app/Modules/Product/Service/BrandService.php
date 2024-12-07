<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Brand;
use Illuminate\Http\Request;

class BrandService
{
    public function create(Request $request): Brand
    {
        return Brand::register(
            $request->string('name')->trim()->value()
        );
    }

    public function setInfo(Request $request, Brand $brand): void
    {
        $brand->name = $request->string('name')->trim()->value();
        $brand->description = $request->string('description')->trim()->value();
        $brand->url = $request->string('url')->trim()->value();
        $brand->setSameAs($request['sameAs']);
        $brand->save();

        $brand->saveImage($request->file('file'), $request->boolean('clear_file'));
    }

    public function delete(Brand $brand): void
    {
        if ($brand->products()->count() > 0) throw new \DomainException('Нельзя удалить бренд с товарами');
        $brand->delete();
    }
}
