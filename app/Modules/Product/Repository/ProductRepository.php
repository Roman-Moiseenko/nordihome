<?php


namespace App\Modules\Product\Repository;


use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Tag;

class ProductRepository
{

    public function getIndex($request, &$filters)
    {
        $query = Product::orderBy('name');
        $filters = [];
        if (($category_id = $request->integer('category')) > 0) {
            $filters['category'] = $category_id;
            //Получить все дочерние категории
            $categories = Category::find($category_id)->getChildrenIdAll();

            $query->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('id', $categories);
            })->orWhereIn('main_category_id', $categories);
        }

        if (!empty($name = $request->string('name')->trim()->value())) {
            $filters['name'] = $name;
            $query->whereRaw("LOWER(name) like LOWER('%$name%')")
                ->orWhere('code', 'like', "%$name%")
                ->orWhere('code_search', 'like', "%$name%");
        }
        if (!is_null($show = $request->input('show'))) {
            $filters['show'] = $show;
            if ($show == 'active') $query->where('published', true);
            if ($show == 'draft') $query->where('published', false);
            if ($show == 'delete') $query->onlyTrashed();
            if ($show == 'not_sale') $query->where('not_sale', true);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Product $product) => $this->ProductToArray($product));
    }

    private function ProductToArray(Product $product): array
    {
        return array_merge($product->toArray(), [
            'image' => $product->miniImage(),
            'category_name' => $product->category->getParentNames(),
            'price' => $product->getPriceRetail(),
            'quantity' => $product->getQuantity(),
            'reserve' => $product->getReserveCount(),
            'trashed' => $product->trashed(),
        ]);
    }

    public function ProductWithToArray(Product $product): array
    {
        return array_merge($this->ProductToArray($product), [
            'equivalents' => Equivalent::orderBy('name')
                ->whereHas('category', function ($query) use ($product) {
                    $query->where('_lft', '<=', $product->category->_lft)
                        ->where('_rgt', '>=', $product->category->_rgt);
                })
                ->get(),
            'categories' => $product->categories()->get()->map(function (Category $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            }),
            'tags' => $product->tags()->get()->map(function (Tag $tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            }),
            'videos' => $product->videos,
            'possible_attributes' => array_filter(array_map(function (Attribute $attribute) use($product) {
                if (!is_null($product->Value($attribute->id))) return false;
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                ];
            }, $product->getPossibleAttribute())),
            'attributes' => $product->prod_attributes()->get()->map(function (Attribute $attribute) use ($product) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'group' => $attribute->group->name,
                    'value' => $attribute->Value(),
                    'variants' => $attribute->variants,
                    'is_variant' => $attribute->isVariant(),
                    'is_bool' => $attribute->isBool(),
                    'is_numeric' => $attribute->isNumeric(),
                    'is_date' => $attribute->isDate(),
                    'is_string' => $attribute->isString(),
                    'multiple' => $attribute->multiple,
                    'is_modification' => $product->AttributeIsModification($attribute->id),
                ];
            }),
            'storages' => $product->storageItems()->get()->map(function (StorageItem $item) {
                return [
                    'id' => $item->id,
                    'name' => $item->storage->name,
                    'cell' => $item->cell,
                ];
            }),
            'balance' => $product->balance,
            'modification' => is_null($product->modification) ? null : [
                'id' => $product->modification->id,
                'name' => $product->modification->name,
                'base_product_id' => $product->modification->base_product_id,
                'attributes' => array_map(function (Attribute $attribute){
                    return [
                        'name' => $attribute->name,
                        'image' => $attribute->getImage(),
                    ];
                }, $product->modification->prod_attributes),
                'products' => $product->modification->products()->get()->map(function (Product $product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'code' => $product->code,
                        'image' => $product->miniImage(),
                        'attributes' => array_map(function (Attribute $attribute) use ($product){
                            return [
                                'name' => $attribute->getVariant($product->Value($attribute->id))->name,
                            ];
                        }, $product->modification->prod_attributes)
                    ];
                }),
            ],
            'equivalent' => is_null($product->equivalent_product) ? null : [
                'id' => $product->equivalent_product->equivalent_id,
                'name' => $product->equivalent->name,
                'products' => $product->equivalent->products()->get()->map(function (Product $product) {
                    return [
                        'id' => $product->id,
                        'code' => $product->code,
                        'name' => $product->name,
                        'image' => $product->miniImage(),
                    ];
                }),
            ],
            'related' => $product->related()->get()->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'image' => $product->miniImage(),
                ];
            }),

            'bonus' => is_null($product->bonus) ? null : $product->bonus()->get()->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'image' => $product->miniImage(),
                    'price' => $product->getPriceRetail(),
                    'discount' => $product->pivot->discount,
                ];
            }),
            'composite' => is_null($product->composites) ? null : $product->composites()->get()->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'image' => $product->miniImage(),
                    'quantity' => $product->pivot->quantity,
                ];
            }),
            'photos' => $product->gallery()->where('imageable_id', $product->id)->get()->map(function (Photo $photo) {
                return [
                    'id' => $photo->id,
                    'name' => $photo->file,
                    'url' => $photo->getThumbUrl('original'),
                    'alt' => $photo->alt,
                    'title' => $photo->title,
                    'description' => $photo->description,
                ];
            }),
        ]);
    }


    public function search(string $search, int $take = 10, array $include_ids = [], bool $isInclude = true): array
    {
        $query = Product::orderBy('name')->where('deleted_at', null)->where(function ($query) use ($search) {
            $query->where('code_search', 'LIKE', "%$search%")->orWhere('code', 'LIKE', "%$search%")
                ->orWhereRaw("LOWER(name) like LOWER('%$search%')");
        });

        if (!empty($include_ids)) {
            if ($isInclude) {
                $query = $query->whereIn('id', $include_ids);
            } else {
                $query = $query->whereNotIn('id', $include_ids);
            }
        }
        return $query->take($take)->getModels();
    }

}
