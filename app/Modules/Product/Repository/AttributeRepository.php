<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeCategory;
use App\Modules\Product\Entity\AttributeProduct;
use App\Modules\Product\Entity\AttributeVariant;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class AttributeRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Attribute::orderBy('name');
        $filters = [];
        if (($category_id = $request->integer('category_id')) > 0) {
            $filters['category_id'] = $category_id;
            $query->whereHas('categories', function ($query) use ($category_id) {
                $query->where('id', $category_id);
            });
        }
        if (($group_id = $request->integer('group_id')) > 0) {
            $filters['group_id'] = $group_id;
            $query->where('group_id', $group_id);
        }
        if (!is_null($filter = $request->input('_filter'))) {
            $filters['_filter'] = $filter;
            $query->where('filter', $filter);
        }
        if (!empty($name = $request->string('name')->trim()->value())) {
            $filters['name'] = $name;
            $query->whereRaw("LOWER(name) like LOWER('%$name%')");
        }
        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Attribute $attribute) => $this->AttributeToArray($attribute));
    }


    private function AttributeToArray(Attribute $attribute): array
    {
        return array_merge($attribute->toArray(), [
            'image' => $attribute->getImage('mini'),
            'categories' => $attribute->categories()->get()->toArray(),
            'group' => $attribute->group->name,
            'type_text' => $attribute->typeText(),
        ]);
    }

    public function AttributeWithToArray(Attribute $attribute): array
    {
        return array_merge($this->AttributeToArray($attribute), [
            'is_variant' => $attribute->isVariant(),
            'variants' => $attribute->variants()->get()->map(function (AttributeVariant $variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'image' => $variant->getImage(),
                ];
            }),
            'image' => $attribute->getImage(),
        ]);
    }



    public function existAndGet(int $id):? Attribute
    {
        try {
            return Attribute::findOrFail($id);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function byName(string $name, int $category_id): Attribute
    {
        $attrs = AttributeCategory::where('category_id', '=', $category_id)->pluck('attribute_id')->toArray();

        return Attribute::where('name', '=', $name)->whereIn('id', $attrs)->first();
    }

    public function getPossibleForCategory(array $parents_id)
    {
        return Attribute::whereHas('categories', function ($query) use ($parents_id) {
            $query->whereIn('category_id', $parents_id);
        })->get();
    }



}
