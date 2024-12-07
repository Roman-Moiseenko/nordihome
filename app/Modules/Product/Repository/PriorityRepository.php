<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;

class PriorityRepository
{

    public function getIndex(Request $request, &$filters)
    {
        $query = Product::where('priority', true);
        $filters = [];

        if ($request->has('product')) {
            $product = $request->string('product');
            $filters['product'] = $product;
            $query->where(function ($q) use ($product) {
                $q->whereRaw("LOWER(name) like LOWER('%$product%')")
                    ->orWhere('code', 'like', "%$product%")
                    ->orWhere('code_search', 'like', "%$product%")
                    ->orWhereHas('series', function ($series) use ($product){
                        $series->whereRaw("LOWER(name) like LOWER('%$product%')");
                    });
            });
        }
        if ($request->has('category')) {
            $category = $request->integer('category');
            $filters['category'] = $category;
            $query->where(function ($qq) use ($filters) {
                $qq->whereHas('categories', function ($q) use ($filters) {
                    $q->where('id', $filters['category']);
                })->orWhere('main_category_id', $filters['category']);
            });
        }
        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Product $product) => $this->ProductToArray($product));
    }

    private function ProductToArray(Product $product): array
    {
        return array_merge($product->toArray(), [
            'category' => $product->category->getParentNames(),
            'quantity' => $product->getQuantity(),
            /**
             * Добавить отношения и вычисления
             */
        ]);
    }
}
