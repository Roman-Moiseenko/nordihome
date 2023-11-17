<?php


namespace App\Modules\Product\Repository;


use App\Modules\Product\Entity\Product;

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
            $query->where('code', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%");
        });

        if (!empty($include_ids)) {
            if ($isInclude) {
                $query = $query->whereIn('id', $include_ids);
            } else {
                $query = $query->whereNotIn('id', $include_ids);
            }
        }

        if (!is_null($take)) {
            $query = $query->take($take);
        } else {
            //$query = $query->all();
        }
        return $query->get();
    }

    public function toArrayForSearch(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'image' => $product->getImage(),
            'price' => $product->lastPrice->value,
        ];
    }

    public function toShopForSearch(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'image' => !is_null($product->photo) ? $product->photo->getThumbUrl('thumb') : '',
            'price' => number_format($product->lastPrice->value, 0, ' ', ','),
            'url' => route('shop.product.view', $product->slug),
        ];
    }

    public function getBySlug($slug): Product
    {
        if (is_numeric($slug)) return Product::findOrFail($slug);
        return Product::where('slug', '=', $slug)->firstOrFail();
    }
}
