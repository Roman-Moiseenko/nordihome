<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\ModificationProduct;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class ModificationRepository
{
    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Modification::orderBy('name');

        $filters = [];
        if (($name = $request->string('name')->trim()->value()) != '') {
            $filters['name'] = $name;
            $query
                ->whereRaw("LOWER(name) like LOWER('%$name%')")
                ->orWhereHas('products', function ($query) use ($name) {
                    $query->whereRaw("LOWER(name) like LOWER('%$name%')")
                        ->orWhere('code', 'like', "%$name%")
                        ->orWhere('code_search', 'like', "%$name%");
                });
        }

        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Modification $modification) => $this->ModificationToArray($modification));

    }

    private function ModificationToArray(Modification $modification): array
    {
        return array_merge($modification->toArray(), [
            'quantity' => $modification->products()->count(),
            'name_attributes' => array_map(function (Attribute $item) {
                return $item->name;
            }, $modification->prod_attributes),
            'image' => $modification->base_product->miniImage(),
        ]);
    }

    public function ModificationWithToArray(Modification $modification): array
    {
        return array_merge($this->ModificationToArray($modification), [
            'base_product' => $modification->base_product,
            'attributes' => array_map(function (Attribute $attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'image' => $attribute->getImage(),
                    'variants' => $attribute->variants()->get()->map(function (AttributeVariant $variant) {
                        return [
                            'id' => $variant->id,
                            'name' => $variant->name,
                            'image' => $variant->getImage(),
                        ];
                    }),
                ];
            }, $modification->prod_attributes),
            'products' => $modification->products()->get()->map(function (Product $product) {
                $values = json_decode($product->pivot->values_json, true);
                $variants = [];
                foreach ($values as $attr_id => $variant_id) {
                    $variants[] = $product->getProdAttribute($attr_id)->getVariant($variant_id)->name;
                }
                return array_merge($product->toArray(), [
                    'image' => $product->miniImage(),
                    'variants' => $variants,
                ]);
            }),
        ]);
    }

    //Массив всех товаров которые входят во все модификации
    public function getAllIdsArray(): array
    {
        $result = array_merge($this->getBaseIdsArray(), $this->getAssignmentIdsArray());
        return array_unique($result);
    }

    public function getAssignmentIdsArray(): array
    {
        return ModificationProduct::orderBy('product_id')->pluck('product_id')->toArray();
    }

    public function getBaseIdsArray(): array
    {
        return Modification::orderBy('id')->pluck('base_product_id')->toArray();
    }

}
