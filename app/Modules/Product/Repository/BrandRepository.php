<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;


class BrandRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Brand::orderBy('name');
        $filters = [];
        if ($request->has('brand')) {
            $name = $request->string('brand')->value();
            $filters['brand'] = $name;
            $query->whereRaw("LOWER(name) like LOWER('%$name%')");
        }
        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Brand $brand) => $this->BrandToArray($brand));
    }


    private function BrandToArray(Brand $brand): array
    {
        return array_merge($brand->toArray(), [
            'quantity' => $brand->products()->count(),
            'image' => $brand->getImage(),
        ]);
    }


    public function byName(string $name): Brand
    {
        return Brand::where('name', '=', $name)->first();
    }

    public function BrandWithToArray(Brand $brand, Request $request): array
    {
        return array_merge($this->BrandToArray($brand), [
            'currency' => $brand->currency,
            'products' => $brand->products()->paginate($request->input('size', 20))
                ->withQueryString()->through(fn(Product $product) => [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category->getParentNames(),
                ]),
        ]);
    }

}
