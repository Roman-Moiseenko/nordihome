<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Series;
use Illuminate\Http\Request;

class SeriesRepository
{

    public function getIndex(Request $request, &$filters)
    {
        $query = Series::orderBy('name');
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
        if ($request->has('series')) {
            $series= $request->string('series')->value();
            $filters['series'] = $series;
            $query->whereRaw("LOWER(name) like LOWER('%$series%')");
        }
        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Series $series) => $this->SeriesToArray($series));
    }

    public function SeriesToArray(Series $series): array
    {
        return array_merge($series->toArray(), [
            'products' => $series->products()->get()->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category->getParentNames(),
                ];
            }),
            'quantity' => $series->products()->count(),
        ]);
    }
}
