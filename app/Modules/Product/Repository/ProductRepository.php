<?php


namespace App\Modules\Product\Repository;


use App\Modules\Product\Entity\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    /**
     * Репозиторий для передачи данных в другие модулю (шлюз)
     * Либо сделать Шлюз get + query
     * \Bus\FletchProduct и \Bus\QueryProduct
     * в QueryProduct setCountForSell(%id, %count) и если нет ТУ то setPriceForSell($id, $price)
     */

    public function existAndGet(int $id): ?Product
    {
        try {
            return Product::findOrFail($id);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getProductJSON(int $id): string
    {
        return json_encode([]);
    }

    public function forCommodity(int $id): string
    {
        /** @var Product $product */
        $product = Product::find($id);
        if (!empty($product)) {
            return json_encode(['code' => $product->code, 'name' => $product->name]);
        }
        return json_encode([]);
    }

    public function search(string $search, int $take = 10, array $include_ids = [], bool $isInclude = true)
    {
        $query = Product::orderBy('name')->where(function ($query) use ($search) {
            $query->where('code_search', 'LIKE', "%{$search}%")->orWhere('code', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%");
        });

        if (!empty($include_ids)) {
            if ($isInclude) {
                $query = $query->whereIn('id', $include_ids);
            } else {
                $query = $query->whereNotIn('id', $include_ids);
            }
        }

        $query = $query->take($take);

        return $query->get();
    }

    public function toArrayForSearch(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'code_search' => $product->code_search,
            'image' => $product->getImage(),
            'price' => $product->getLastPrice(),
            'url' => route('admin.product.edit', $product),
        ];
    }


    public function getFilter(array $filters)
    {

        //$published = $request['published'] ?? 'all';
        $query = Product::orderBy('name');
        $product = $filters['product'];


        if (!empty($filters['product'])) $query->where(function ($q) use ($product) {
            $q->where('name', 'like', "%$product%")
                ->orWhere('code', 'like', "%$product%")
                ->orWhere('code_search', 'like', "%$product%")
                ->orWhereHas('series', function ($series) use ($product){
                    $series->where('name', 'like', "%$product%");
                });
        });

        if (!empty($filters['category'])) {
            $query->where(function ($qq) use ($filters) {
               $qq->whereHas('categories', function ($q) use ($filters) {
                   $q->where('id', $filters['category']);
               })->orWhere('main_category_id', $filters['category']);
            });
        }
        if ($filters['published'] == 'active') $query->where('published', '=', true);
        if ($filters['published'] == 'draft') $query->where('published', '=', false);
        return $query;
    }


}
