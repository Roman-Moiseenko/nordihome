<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class EquivalentRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Equivalent::orderBy('name');
        $filters = [];

        if ($request->has('product')) {
            $product = $request->string('product');
            $filters['product'] = $product;
            $query->whereHas('products', function ($query) use ($product) {
                $query->where(function ($q) use ($product) {
                    $q->whereRaw("LOWER(name) like LOWER('%$product%')")
                        ->orWhere('code', 'like', "%$product%")
                        ->orWhere('code_search', 'like', "%$product%");
                });
            });

        }
        if ($request->has('name')) {
            $name = $request->string('name')->value();
            $filters['name'] = $name;
            $query->whereRaw("LOWER(name) like LOWER('%$name%')");
        }
        if ($request->has('category')) {
            $category = $request->integer('category');
            $filters['category'] = $category;
            $query->where('category_id', $category);
            //TODO Дочерние категории
        }

        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Equivalent $equivalent) => $this->EquivalentToArray($equivalent));
    }

    private function EquivalentToArray(Equivalent $equivalent): array
    {
        return array_merge($equivalent->toArray(), [
            'category' => $equivalent->category->getParentNames(),
            'quantity' => $equivalent->products()->count(),
        ]);
    }

    public function EquivalentWithToArray(Equivalent $equivalent): array
    {
        return array_merge($this->EquivalentToArray($equivalent), [
            'products' => $equivalent->products()->get()->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category->getParentNames(),
                ];
            }),
        ]);
    }

    public function search(Equivalent $equivalent, Request $request)
    {
        $search = $request->string('search')->trim()->value();
        return Product::orderBy('name')
            ->where(function ($query) use ($search) {
                $query->where('code_search', 'LIKE', "%{$search}%")->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            })
            ->where('main_category_id', '>=', $equivalent->category->_lft)
            ->where('main_category_id', '<=', $equivalent->category->_rgt)
            ->doesntHave('equivalent')
            ->get()->map(fn(Product $product) => $product->toArrayForSearch());
    }
}
